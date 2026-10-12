<?php

namespace App\Services\App;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Notifications on the customer's phone (the SBK app) when the desk writes or
 * calls - the SBK Chat app's push (sbk-chat/includes/push.php), on this
 * website's chat.
 *
 * Firebase Cloud Messaging v1, project "sbk-chat" (the SBK Chat app's own), with
 * the project's service-account key read from FCM_SERVICE_ACCOUNT (a file
 * outside the web root; never in git). Without it, or without the table,
 * nothing is sent and the chat works exactly as before.
 *
 * Sending happens AFTER the response has gone (Laravel's terminating
 * callbacks), so a reply on the desk is never slowed by Google.
 *
 * The message is FCM "data only" with the keys expo-notifications turns into a
 * notification on Android (title, message, channelId, tag ...), as the SBK Chat
 * app proved on real phones; `body` carries what the app needs on a tap.
 */
class AppPush
{
    private static array $queue = [];
    private static bool $hooked = false;

    /** A message in a conversation: the customer hears about the desk's (and a missed call). */
    public static function afterMessage(int $threadId, bool $fromGuest, ?int $staffId, string $kind, string $body, bool $counted): void
    {
        if ($fromGuest || !$counted || !self::ready()) {
            return;
        }
        $thread = DB::table('sbk_chat_threads')->where('id', $threadId)->first(['id', 'guest_id', 'staff_id']);
        if (!$thread) {
            return;
        }
        $from = self::staffName((int) ($staffId ?: $thread->staff_id));
        if ($kind === 'call') {
            $parts = explode('|', $body);
            $what = ($parts[0] ?? '') === 'video' ? 'video call' : 'voice call';
            $text = 'Missed ' . $what . ' from ' . $from;
            $channel = 'calls';
        } else {
            $text = $kind === 'image' ? 'Picture' : ($kind === 'voice' ? 'Voice note' : mb_substr(trim($body), 0, 180));
            $channel = 'messages';
        }
        self::queue((int) $thread->guest_id, [
            'title' => $from,
            'message' => $text,
            'channelId' => $channel,
            'tag' => 't' . $thread->id,
            'body' => json_encode(['type' => 'message', 'thread' => (int) $thread->id, 'to' => (int) $thread->staff_id, 'name' => $from]),
        ]);
    }

    /** The desk rings a customer. */
    public static function ringing(int $callId): void
    {
        if (!self::ready()) {
            return;
        }
        $call = DB::table('sbk_chat_calls as c')->join('sbk_chat_threads as t', 't.id', '=', 'c.thread_id')
            ->where('c.id', $callId)->first(['c.id', 'c.kind', 'c.from_guest', 't.id as thread', 't.guest_id', 't.staff_id']);
        if (!$call || (int) $call->from_guest === 1) {
            return;
        }
        $from = self::staffName((int) $call->staff_id);
        self::queue((int) $call->guest_id, [
            'title' => $from,
            'message' => 'Incoming ' . ($call->kind === 'video' ? 'video' : 'voice') . ' call',
            'channelId' => 'calls',
            'tag' => 'call' . $call->id,
            'body' => json_encode(['type' => 'call', 'call' => (int) $call->id, 'thread' => (int) $call->thread, 'to' => (int) $call->staff_id, 'name' => $from]),
        ]);
    }

    /* ------------------------------------------------------------ sending */

    private static ?bool $ready = null;

    public static function ready(): bool
    {
        if (self::$ready === null) {
            $file = (string) config('services.fcm.service_account');
            self::$ready = $file !== '' && is_readable($file) && Schema::hasTable('sbk_app_push');
        }

        return self::$ready;
    }

    private static function staffName(int $adminId): string
    {
        $a = DB::table('admins')->where('id', $adminId)->first(['first_name', 'last_name', 'email']);
        $name = $a ? trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')) : '';

        return $name !== '' ? $name : 'SBK Global Auto Trading';
    }

    private static function queue(int $guestId, array $data): void
    {
        self::$queue[] = [$guestId, $data];
        if (!self::$hooked) {
            self::$hooked = true;
            app()->terminating(function () {
                $jobs = self::$queue;
                self::$queue = [];
                self::$hooked = false;
                foreach ($jobs as [$guestId, $data]) {
                    self::send($guestId, $data);
                }
            });
        }
    }

    /** To every phone of that customer; a phone that no longer has the app is forgotten. */
    public static function send(int $guestId, array $data): int
    {
        try {
            $tokens = DB::table('sbk_app_push')->where('who', 'guest')->where('who_id', $guestId)->pluck('token');
            if ($tokens->isEmpty()) {
                return 0;
            }
            $access = self::accessToken();
            $project = (string) config('services.fcm.project', 'sbk-chat');
            if (!$access) {
                return 0;
            }
            $sent = 0;
            foreach ($tokens as $token) {
                $r = Http::withToken($access)->acceptJson()->timeout(10)
                    ->post("https://fcm.googleapis.com/v1/projects/{$project}/messages:send", [
                        'message' => [
                            'token' => $token,
                            'data' => array_map('strval', $data),
                            'android' => ['priority' => 'HIGH', 'ttl' => ($data['channelId'] ?? '') === 'calls' ? '45s' : '86400s'],
                        ],
                    ]);
                if ($r->successful()) {
                    $sent++;
                } elseif (in_array($r->status(), [400, 404], true) && str_contains($r->body(), 'UNREGISTERED')
                    || str_contains($r->body(), 'registration-token-not-registered')) {
                    DB::table('sbk_app_push')->where('token', $token)->delete();
                } else {
                    Log::warning('App push not sent', ['status' => $r->status(), 'body' => mb_substr($r->body(), 0, 300)]);
                }
            }

            return $sent;
        } catch (\Throwable $e) {
            Log::warning('App push failed', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    /** Google's OAuth token for FCM from the service-account key, kept for its hour. */
    private static function accessToken(): ?string
    {
        return Cache::remember('app-push:fcm-token', 3000, function () {
            $key = json_decode((string) @file_get_contents((string) config('services.fcm.service_account')), true);
            if (!$key || empty($key['private_key']) || empty($key['client_email'])) {
                return null;
            }
            $now = time();
            $b64 = fn ($v) => rtrim(strtr(base64_encode($v), '+/', '-_'), '=');
            $head = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claim = $b64(json_encode([
                'iss' => $key['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]));
            $sig = '';
            if (!openssl_sign($head . '.' . $claim, $sig, $key['private_key'], OPENSSL_ALGO_SHA256)) {
                return null;
            }
            $r = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $head . '.' . $claim . '.' . $b64($sig),
            ]);

            return $r->successful() ? $r->json('access_token') : null;
        }) ?: null;
    }
}
