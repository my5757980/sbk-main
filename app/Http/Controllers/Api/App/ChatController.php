<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Services\Chat\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * The chat's way in, for the SBK app - Chat\ChatPageController@start with the
 * same checks, answering JSON instead of a page.
 *
 * A visitor gives name, e-mail and telephone once and gets the chat's visitor
 * token back to keep on the phone (the browser keeps the same value in a
 * cookie). A signed-in customer is already known by their account token and is
 * only asked for a name or telephone the account does not have.
 */
class ChatController extends Controller
{
    public function start(Request $request, ChatService $chat)
    {
        if ($s = $chat->staff()) {
            return ChatService::json(['ok' => true, 'staff' => true, 'me' => ['name' => $s['name'], 'email' => $s['email'] ?? '']]);
        }

        $user = auth('web')->user();
        $g = $chat->guest();
        $details = $g && ChatService::guestNeedsDetails($g);

        if ($request->isMethod('get') || (!$request->hasAny(['name', 'email', 'phone']) && $g && !$details)) {
            if (!$g) {
                return ChatService::json(['ok' => false, 'signedout' => true], 401);
            }

            return ChatService::json(self::answer($g, $user, $details));
        }

        $fixedName = '';
        if ($details && $user) {
            $real = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            if ($real !== '' && !ChatService::looksLikeLogin($real, '', $g['email'])) {
                $fixedName = $real;
            }
        }

        $name = $fixedName !== '' ? $fixedName : trim((string) $request->input('name', ''));
        $email = ($details || $user) ? (string) ($g['email'] ?? $user->email) : trim((string) $request->input('email', ''));
        $phone = trim((string) $request->input('phone', ''));

        // The website's own checks (ChatPageController@start).
        if (mb_strlen($name) < 2 || (($details || $user) && str_contains($name, '@'))) {
            return ChatService::json(['ok' => false, 'error' => 'Please tell us your name.'], 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ChatService::json(['ok' => false, 'error' => 'That e-mail address does not look right.'], 422);
        }
        if (strlen(preg_replace('/\D+/', '', $phone)) < 7) {
            return ChatService::json(['ok' => false, 'error' => 'Please give a telephone number we can reach you on.'], 422);
        }

        if ($g && ($details || $user)) {
            $chat->guestSaveDetails((int) $g['id'], $name, $phone);
            $g = (array) DB::table('sbk_chat_guests')->where('id', $g['id'])->first();
        } else {
            $g = $chat->guestSignIn($name, $email, $phone);
        }

        return ChatService::json(self::answer($g, $user, false));
    }

    /** Who the chat now knows this phone as - and, for a visitor, the token to keep. */
    private static function answer(array $g, $user, bool $details): array
    {
        return [
            'ok' => true,
            'staff' => false,
            'details' => $details,
            'guestToken' => $user ? null : (string) $g['token'],
            'me' => ['name' => (string) $g['name'], 'email' => (string) $g['email'], 'phone' => (string) $g['phone']],
        ];
    }
}
