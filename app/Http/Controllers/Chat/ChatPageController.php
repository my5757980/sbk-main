<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Services\Chat\ChatService;
use Illuminate\Http\Request;

/**
 * The chat's pages: the conversation (chat.php of the SBK chat), the way in for
 * visitors (index.php), leaving (logout.php) and the desk's door
 * (staff-login.php). They may be framed only by this website's own panel.
 */
class ChatPageController extends Controller
{
    private const FRAME_GUARD = "frame-ancestors 'self'";

    /**
     * One page for both sides: a customer sees the team, the desk sees its
     * conversations - and someone who sees everything gets agents first.
     */
    public function app(Request $request, ChatService $chat)
    {
        $g = $chat->guest();
        $s = $chat->staff();
        if (!$g && !$s) {
            return redirect()->route('chat.start');
        }
        $isGuest = $g && !$s;

        // Name, e-mail and telephone before anything else, for everybody who is not the desk.
        if ($isGuest && ChatService::guestNeedsDetails($g)) {
            return redirect()->route('chat.start');
        }

        $me = $isGuest ? $g : $s;
        $seesAll = !$isGuest && $chat->staffSeesEverything();
        // A customer whose only "name" is the front of their e-mail is asked once for a real one.
        $needsName = $isGuest && ChatService::looksLikeLogin($g['name'], '', $g['email']);

        return response()
            ->view('chat.app', [
                'isGuest' => $isGuest,
                'me' => $me,
                'seesAll' => $seesAll,
                'needsName' => $needsName,
                'openTo' => (int) $request->query('to', 0),
            ])
            ->header('Content-Security-Policy', self::FRAME_GUARD);
    }

    /**
     * The way in: a visitor gives name, e-mail and telephone once. A customer
     * signed in on the website only sees this if their phone (or a real name)
     * is missing, with the e-mail from their account fixed.
     */
    public function start(Request $request, ChatService $chat)
    {
        if ($chat->staff()) {
            return redirect()->route('chat.app');
        }

        $g = $chat->guest();
        $details = $g && ChatService::guestNeedsDetails($g);
        $fixedName = '';
        if ($details && ($user = auth('web')->user())) {
            $real = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            if ($real !== '' && !ChatService::looksLikeLogin($real, '', $g['email'])) {
                $fixedName = $real;
            }
        }

        if ($g && !$details && !$request->boolean('new')) {
            return redirect()->route('chat.app');
        }

        $err = '';
        if ($request->isMethod('post')) {
            $name = $fixedName !== '' ? $fixedName : trim((string) $request->input('name', ''));
            $email = $details ? (string) $g['email'] : trim((string) $request->input('email', ''));
            $phone = trim((string) $request->input('phone', ''));

            // Checked here and not only in the browser: a form can be posted without one.
            if (mb_strlen($name) < 2 || ($details && str_contains($name, '@'))) {
                $err = 'Please tell us your name.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $err = 'That e-mail address does not look right.';
            } elseif (strlen(preg_replace('/\D+/', '', $phone)) < 7) {
                $err = 'Please give a telephone number we can reach you on.';
            } else {
                if ($details) {
                    $chat->guestSaveDetails((int) $g['id'], $name, $phone);
                } else {
                    $chat->guestSignIn($name, $email, $phone);
                }

                return redirect()->route('chat.app');
            }
        }

        if ($details) {
            $name = $fixedName !== '' ? $fixedName
                : (string) $request->input('name', ChatService::looksLikeLogin($g['name'], '', $g['email']) ? '' : $g['name']);
            $email = (string) $g['email'];
            $phone = (string) $request->input('phone', $g['phone']);
        } else {
            $name = (string) $request->input('name', '');
            $email = (string) $request->input('email', '');
            $phone = (string) $request->input('phone', '');
        }

        return response()
            ->view('chat.start', compact('details', 'fixedName', 'err', 'name', 'email', 'phone'))
            ->header('Content-Security-Policy', self::FRAME_GUARD);
    }

    /**
     * Leave the chat - only the chat, never the website. The green dot goes off
     * now rather than 45 seconds later; the conversation is kept.
     */
    public function logout(ChatService $chat)
    {
        $s = $chat->staff();
        $g = $chat->guest();
        if ($s) {
            $chat->forgetPresence('staff', (int) $s['id']);
        }
        if ($g) {
            $chat->forgetPresence('guest', (int) $g['id']);
            $chat->forgetGuestCookie();
        }

        return $s ? redirect()->route('admin.dashboard.index') : redirect()->route('index');
    }

    /** The desk signs in on the admin panel, not here. */
    public function desk(ChatService $chat)
    {
        if ($chat->staff() || $chat->guest()) {
            return redirect()->route('chat.app');
        }

        return redirect()->route('admin.auth.login');
    }

    /** "Already have an account? Sign in" - on the website, then straight back to the chat. */
    public function login()
    {
        if (auth('web')->check()) {
            return redirect()->route('chat.app');
        }

        return redirect()->guest(route('login'));
    }
}
