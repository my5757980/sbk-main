<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Services\Chat\ChatService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * The chat's live channel and messages - api/poll.php, send.php, upload.php,
 * name.php, ping.php and ice.php of the SBK chat, same answers, same rules.
 */
class ChatApiController extends Controller
{
    /**
     * One request answers everything a screen needs: new messages in the open
     * thread or group, the side list with who is online, the number waiting,
     * and a call if this person is in one or being rung. It polls (answers and
     * closes) rather than holding a connection open.
     */
    public function poll(Request $request, ChatService $chat)
    {
        $g = $chat->guest();
        $s = $chat->staff();
        if (!$g && !$s) {
            return ChatService::json(['ok' => false, 'signedout' => true], 401);
        }
        ChatService::refuseUntilDetails($g, $s);

        $viewerIsGuest = $g && !$s;
        $meId = (int) ($viewerIsGuest ? $g['id'] : $s['id']);
        $chat->touchPresence($viewerIsGuest ? 'guest' : 'staff', $meId, 'chat');

        $out = ['ok' => true, 'now' => gmdate('c')];

        // A ring reaches the screen on the poll that already repeats.
        $chat->sweepCalls();
        $live = $chat->liveCallFor($viewerIsGuest, $meId);
        if ($live) {
            $chat->touchCall($live, $viewerIsGuest);
            $out['call'] = $chat->callOut($live, $viewerIsGuest);
        }

        $after = (int) $request->query('after', 0);

        // The group that is open - membership and nothing else.
        $groupId = (int) $request->query('group', 0);
        if ($groupId > 0 && !$viewerIsGuest) {
            if (!$chat->mayReadGroup($groupId)) {
                return ChatService::json(['ok' => false, 'error' => 'not yours'], 403);
            }
            $out['messages'] = $chat->groupMessagesSince($groupId, $after, (int) $s['id']);
            if ($request->query('open')) {
                $chat->markGroupRead($groupId, (int) $s['id']);
            }
            $out['receipts'] = $chat->groupReceipts($groupId, (int) $s['id']);
        }

        // The thread that is open, and the ticks on our own messages in it.
        $threadId = (int) $request->query('thread', 0);
        if ($threadId > 0 && $groupId <= 0) {
            if (!$chat->mayReadThread($threadId)) {
                return ChatService::json(['ok' => false, 'error' => 'not yours'], 403);
            }
            $out['messages'] = $chat->messagesSince($threadId, $after, $viewerIsGuest);
            if ($request->query('open')) {
                $chat->markRead($threadId, $viewerIsGuest);
            }
            $out['receipts'] = $chat->receipts($threadId, $viewerIsGuest);
        }

        if ($viewerIsGuest) {
            $people = [];
            foreach ($chat->staffDirectory() as $p) {
                $people[] = [
                    'id' => (int) $p['id'],
                    'name' => $p['name'],
                    'role' => (string) $p['role_label'],
                    'online' => $p['online'],
                    'ini' => ChatService::initials($p['name']),
                    'hue' => ChatService::avatarHue($p['id'] . $p['name']),
                ];
            }
            $out['people'] = $people;
            $out['unread'] = $chat->unreadFor('guest', (int) $g['id']);

            $per = [];
            foreach (DB::table('sbk_chat_threads')->where('guest_id', $g['id'])->get(['id', 'staff_id', 'guest_unread']) as $row) {
                $per[(int) $row->staff_id] = ['thread' => (int) $row->id, 'unread' => (int) $row->guest_unread];
            }
            $out['threads'] = $per;

            if ($request->query('me')) {
                $out['me'] = ['name' => (string) $g['name'],
                              'needs_name' => ChatService::looksLikeLogin($g['name'], '', $g['email'])];
            }
        } else {
            $all = $chat->staffSeesEverything();
            $list = [];
            foreach ($chat->staffThreads((int) $s['id'], $all) as $t) {
                $list[] = [
                    'id' => (int) $t['id'],
                    'name' => $t['guest_name'],
                    'email' => $t['guest_email'],
                    'phone' => $t['guest_phone'],
                    'staff' => $all ? (string) ($t['staff_name'] ?? '') : '',
                    'sid' => (int) $t['staff_id'],
                    'mine' => (int) $t['staff_id'] === (int) $s['id'],
                    'preview' => (string) $t['last_preview'],
                    'at' => $t['last_message_at'],
                    'unread' => (int) $t['staff_unread'],
                    'online' => $t['guest_online'],
                    'ini' => ChatService::initials($t['guest_name']),
                    'hue' => ChatService::avatarHue($t['guest_email']),
                ];
            }
            $out['list'] = $list;
            $out['all'] = $all;

            $groups = [];
            foreach ($chat->groupsFor((int) $s['id']) as $gr) {
                $groups[] = [
                    'id' => (int) $gr['id'],
                    'name' => (string) $gr['name'],
                    'preview' => (string) $gr['last_preview'],
                    'at' => $gr['last_message_at'],
                    'unread' => (int) $gr['unread'],
                    'members' => (int) $gr['members'],
                    'owner' => (int) $gr['is_owner'] === 1,
                    'ini' => ChatService::initials((string) $gr['name']),
                    'hue' => ChatService::avatarHue('g' . $gr['id'] . $gr['name']),
                ];
            }
            $out['groups'] = $groups;
            $out['unread'] = $chat->unreadFor('staff', (int) $s['id']) + $chat->groupUnreadFor((int) $s['id']);
        }

        return ChatService::json($out);
    }

    /**
     * Send a message. A customer names the member of staff (`to`) and the thread
     * is made on their first word; the desk answers in a thread (or group) that
     * is theirs - checked here, not in the markup that drew the reply box.
     */
    public function send(Request $request, ChatService $chat)
    {
        $g = $chat->guest();
        $s = $chat->staff();
        if (!$g && !$s) {
            return ChatService::json(['ok' => false, 'signedout' => true], 401);
        }
        ChatService::refuseUntilDetails($g, $s);
        $viewerIsGuest = $g && !$s;

        $body = trim((string) $request->input('body', ''));
        if ($body === '') {
            return ChatService::json(['ok' => false, 'error' => 'Nothing to send.'], 400);
        }
        $body = mb_substr($body, 0, 4000);

        if ($viewerIsGuest) {
            $t = $this->threadToStaff($chat, $g, (int) $request->input('to', 0));
            $id = $chat->addMessage((int) $t['id'], true, null, 'text', $body);
        } else {
            $groupId = (int) $request->input('group', 0);
            if ($groupId > 0) {
                if (!$chat->mayReadGroup($groupId)) {
                    return ChatService::json(['ok' => false, 'error' => 'That group is not yours.'], 403);
                }
                $id = $chat->addGroupMessage($groupId, (int) $s['id'], 'text', $body);

                return ChatService::json(['ok' => true, 'group' => $groupId,
                    'message' => $chat->groupMessageOut($chat->messageRow($id), (int) $s['id'],
                        $chat->staffNames([(int) $s['id']]))]);
            }
            $t = $chat->mayReadThread((int) $request->input('thread', 0));
            if (!$t) {
                return ChatService::json(['ok' => false, 'error' => 'That conversation is not yours.'], 403);
            }
            $id = $chat->addMessage((int) $t['id'], false, (int) $s['id'], 'text', $body);
        }

        // The sender gets their own message straight back.
        return ChatService::json(['ok' => true, 'thread' => (int) $t['id'],
            'message' => $chat->messageOut($chat->messageRow($id), $viewerIsGuest)]);
    }

    /**
     * The person a customer writes to must be listed staff right now - a made-up
     * id or another customer's is refused even from a request that never came
     * from the list.
     */
    private function threadToStaff(ChatService $chat, array $g, int $staffId, ?string $cleanup = null): array
    {
        $who = $chat->person($staffId);
        if (!$who || !$who['is_listed']) {
            if ($cleanup) {
                @unlink($cleanup);
            }
            ChatService::stop(['ok' => false, 'error' => 'That person is not available.'], 400);
        }

        return $chat->threadFor((int) $g['id'], $staffId);
    }

    /**
     * A picture or a voice note. Sizes capped, pictures re-encoded (smaller, and
     * the camera's location data gone), files spread over two directory levels,
     * nothing under the public folder.
     */
    public function upload(Request $request, ChatService $chat)
    {
        $g = $chat->guest();
        $s = $chat->staff();
        if (!$g && !$s) {
            return ChatService::json(['ok' => false, 'signedout' => true], 401);
        }
        ChatService::refuseUntilDetails($g, $s);
        $viewerIsGuest = $g && !$s;

        $kind = $request->input('kind') === 'voice' ? 'voice' : 'image';
        $f = $request->file('file');
        if (!$f instanceof UploadedFile || !$f->isValid()) {
            $why = [
                UPLOAD_ERR_INI_SIZE => 'That file is larger than the server accepts.',
                UPLOAD_ERR_FORM_SIZE => 'That file is too large.',
                UPLOAD_ERR_PARTIAL => 'The upload was cut short. Try again.',
                UPLOAD_ERR_NO_FILE => 'No file arrived.',
            ];
            $code = $f instanceof UploadedFile ? $f->getError() : UPLOAD_ERR_NO_FILE;

            return ChatService::json(['ok' => false, 'error' => $why[$code] ?? 'The upload failed.'], 400);
        }

        $max = $kind === 'voice' ? (int) config('chat.max_voice_bytes') : (int) config('chat.max_image_bytes');
        if ((int) $f->getSize() > $max) {
            return ChatService::json(['ok' => false,
                'error' => 'That is too large. The limit is ' . round($max / 1048576) . ' MB.'], 400);
        }

        // What it says it is counts for nothing; what it IS decides.
        $mime = (string) (new \finfo(FILEINFO_MIME_TYPE))->file($f->getRealPath());
        $okImage = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        $okVoice = ['audio/webm' => 'webm', 'video/webm' => 'webm', 'audio/ogg' => 'ogg',
                    'audio/mpeg' => 'mp3', 'audio/mp4' => 'm4a', 'audio/x-m4a' => 'm4a',
                    'video/mp4' => 'm4a', 'video/3gpp' => '3gp', 'audio/3gpp' => '3gp', 'audio/amr' => 'amr',
                    'audio/wav' => 'wav', 'audio/x-wav' => 'wav', 'audio/aac' => 'aac'];
        $ext = $kind === 'voice' ? ($okVoice[$mime] ?? null) : ($okImage[$mime] ?? null);
        if ($ext === null) {
            logger()->info('chat upload refused', ['kind' => $kind, 'mime' => $mime, 'bytes' => (int) $f->getSize()]);

            return ChatService::json(['ok' => false,
                'error' => $kind === 'voice' ? 'That is not a sound file.' : 'That is not a picture.'], 400);
        }

        // storage/app/chat-media/ab/cd/<random>.<ext>
        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        $rel = substr($name, 0, 2) . '/' . substr($name, 2, 2) . '/' . $name;
        $abs = ChatService::mediaDir() . '/' . $rel;
        if (!is_dir(dirname($abs)) && !@mkdir(dirname($abs), 0755, true)) {
            return ChatService::json(['ok' => false, 'error' => 'Could not store that just now.'], 500);
        }

        $secs = null;
        if ($kind === 'image') {
            if (!self::shrinkImage($f->getRealPath(), $abs, $mime, 1600, 82) && !@copy($f->getRealPath(), $abs)) {
                return ChatService::json(['ok' => false, 'error' => 'Could not store that picture.'], 500);
            }
        } else {
            if (!@copy($f->getRealPath(), $abs)) {
                return ChatService::json(['ok' => false, 'error' => 'Could not store that recording.'], 500);
            }
            $secs = (int) $request->input('secs', 0);
            if ($secs < 0 || $secs > 36000) {
                $secs = null;
            }
        }
        @chmod($abs, 0644);
        $bytes = (int) @filesize($abs);

        if ($viewerIsGuest) {
            $t = $this->threadToStaff($chat, $g, (int) $request->input('to', 0), $abs);
            $id = $chat->addMessage((int) $t['id'], true, null, $kind, '', $rel, $mime, $bytes, $secs);
        } else {
            $groupId = (int) $request->input('group', 0);
            if ($groupId > 0) {
                if (!$chat->mayReadGroup($groupId)) {
                    @unlink($abs);

                    return ChatService::json(['ok' => false, 'error' => 'That group is not yours.'], 403);
                }
                $id = $chat->addGroupMessage($groupId, (int) $s['id'], $kind, '', $rel, $mime, $bytes, $secs);

                return ChatService::json(['ok' => true, 'group' => $groupId,
                    'message' => $chat->groupMessageOut($chat->messageRow($id), (int) $s['id'],
                        $chat->staffNames([(int) $s['id']]))]);
            }
            $t = $chat->mayReadThread((int) $request->input('thread', 0));
            if (!$t) {
                @unlink($abs);

                return ChatService::json(['ok' => false, 'error' => 'That conversation is not yours.'], 403);
            }
            $id = $chat->addMessage((int) $t['id'], false, (int) $s['id'], $kind, '', $rel, $mime, $bytes, $secs);
        }

        return ChatService::json(['ok' => true, 'thread' => (int) $t['id'],
            'message' => $chat->messageOut($chat->messageRow($id), $viewerIsGuest)]);
    }

    /** A smaller copy of a picture; false when GD cannot read it (the original is kept). */
    private static function shrinkImage(string $src, string $dst, string $mime, int $maxSide, int $quality): bool
    {
        if (!function_exists('imagecreatetruecolor')) {
            return false;
        }
        $img = null;
        if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
            $img = @imagecreatefromjpeg($src);
        } elseif ($mime === 'image/png' && function_exists('imagecreatefrompng')) {
            $img = @imagecreatefrompng($src);
        } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $img = @imagecreatefromwebp($src);
        } elseif ($mime === 'image/gif') {
            return false;       // left whole: shrinking would stop the animation
        }
        if (!$img) {
            return false;
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $scale = min(1.0, $maxSide / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));
        $out = imagecreatetruecolor($nw, $nh);
        // A white ground, so a transparent PNG does not arrive as a black rectangle.
        imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
        imagecopyresampled($out, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);

        $ok = false;
        if (str_ends_with($dst, '.png') && function_exists('imagepng')) {
            $ok = @imagepng($out, $dst, 6);
        } elseif (str_ends_with($dst, '.webp') && function_exists('imagewebp')) {
            $ok = @imagewebp($out, $dst, $quality);
        } elseif (function_exists('imagejpeg')) {
            $ok = @imagejpeg($out, $dst, $quality);
        }
        imagedestroy($img);
        imagedestroy($out);

        return (bool) $ok;
    }

    /**
     * "What should we call you?" - a signed-in customer with no real name on
     * file. Kept in the chat's own row, never written into the website account.
     */
    public function name(Request $request, ChatService $chat)
    {
        $g = $chat->guest();
        if (!$g || $chat->staff()) {
            return ChatService::json(['ok' => false, 'signedout' => true], 401);
        }
        $name = trim(preg_replace('/\s+/u', ' ', (string) $request->input('name', '')));
        if (mb_strlen($name) < 2) {
            return ChatService::json(['ok' => false, 'error' => 'Please type your name.'], 400);
        }
        if (str_contains($name, '@')) {
            return ChatService::json(['ok' => false, 'error' => 'That looks like an e-mail address — just your name, please.'], 400);
        }
        $name = mb_substr($name, 0, 80);
        DB::table('sbk_chat_guests')->where('id', $g['id'])->update(['name' => $name]);

        return ChatService::json(['ok' => true, 'name' => $name]);
    }

    /**
     * "I am still here." Every page of the website (and the admin panel) sends
     * it, so a member of staff reading the website is green. Answers with the
     * number waiting, the newest message for the card, and a call ringing.
     */
    public function ping(Request $request, ChatService $chat)
    {
        $where = in_array($request->query('from'), ['chat', 'site'], true) ? $request->query('from') : 'chat';

        $s = $chat->staff();
        if ($s) {
            $chat->touchPresence('staff', (int) $s['id'], $where);

            return ChatService::json(['ok' => true, 'who' => 'staff', 'staff' => true, 'name' => $s['name'],
                'unread' => $chat->unreadFor('staff', (int) $s['id']),
                'last' => $chat->latestWaiting('staff', (int) $s['id']),
                'ringing' => $this->ringingFor($chat, false, (int) $s['id'])]);
        }

        // A signed-in customer's row is refreshed from their account on every beat.
        $user = auth('web')->user();
        $g = ($user && ($user->role ?? 'customer') === 'customer') ? $chat->guestForUser($user) : $chat->guest();
        if ($g) {
            $chat->touchPresence('guest', (int) $g['id'], $where);

            return ChatService::json(['ok' => true, 'who' => 'guest', 'staff' => false, 'name' => $g['name'],
                'unread' => $chat->unreadFor('guest', (int) $g['id']),
                'last' => $chat->latestWaiting('guest', (int) $g['id']),
                'ringing' => $this->ringingFor($chat, true, (int) $g['id'])]);
        }

        return ChatService::json(['ok' => false, 'signedout' => true], 401);
    }

    /** Is this person being rung right now? For the website's corner card. */
    private function ringingFor(ChatService $chat, bool $viewerIsGuest, int $meId): ?array
    {
        $chat->sweepCalls();
        $c = $chat->liveCallFor($viewerIsGuest, $meId);
        if (!$c || $c['state'] !== 'ringing' || !ChatService::callIsIncoming($c, $viewerIsGuest)) {
            return null;
        }

        return ['id' => (int) $c['id'], 'from' => $chat->callPeerName($c, $viewerIsGuest),
                'kind' => $c['kind'], 'thread' => (int) $c['thread_id']];
    }

    /**
     * Where two browsers should look for each other: public STUN always, and a
     * relay (TURN) when one is configured. The relay's key stays on the server.
     */
    public function ice(ChatService $chat)
    {
        if (!$chat->guest() && !$chat->staff()) {
            return ChatService::json(['ok' => false, 'signedout' => true], 401);
        }
        ChatService::refuseUntilDetails($chat->guest(), $chat->staff());

        $servers = [
            ['urls' => ['stun:stun.l.google.com:19302', 'stun:stun1.l.google.com:19302']],
            ['urls' => 'stun:stun.cloudflare.com:3478'],
        ];
        $relay = false;

        $app = (string) config('chat.metered_app');
        $key = (string) config('chat.metered_key');
        if ($app !== '' && $key !== '') {
            $got = Cache::remember('chat:metered:' . md5($app . $key), 600, function () use ($app, $key) {
                try {
                    $r = Http::timeout(6)->get('https://' . rawurlencode($app) . '.metered.live/api/v1/turn/credentials',
                        ['apiKey' => $key]);

                    return $r->successful() && is_array($r->json()) ? $r->json() : null;
                } catch (\Throwable $e) {
                    return null;
                }
            });
            foreach ((array) $got as $srv) {
                if (!empty($srv['urls'])) {
                    $servers[] = $srv;
                    $relay = $relay || str_contains(json_encode($srv['urls']), 'turn');
                }
            }
        }

        $turnUrls = (string) config('chat.turn_urls');
        if ($turnUrls !== '') {
            $servers[] = [
                'urls' => array_values(array_filter(array_map('trim', explode(',', $turnUrls)))),
                'username' => (string) config('chat.turn_user'),
                'credential' => (string) config('chat.turn_pass'),
            ];
            $relay = true;
        }

        return ChatService::json(['ok' => true, 'iceServers' => $servers, 'relay' => $relay]);
    }
}
