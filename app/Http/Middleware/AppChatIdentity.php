<?php

namespace App\Http\Middleware;

use App\Services\Chat\ChatService;
use Closure;
use Illuminate\Http\Request;

/**
 * Who is chatting, for the SBK app's chat routes (routes/app.php).
 *
 * The website's chat (ChatService) knows people by the browser's session or by
 * the visitor cookie. The app has neither: a signed-in customer sends their
 * account token, a visitor sends the chat's own visitor token (the value the
 * browser keeps in the sbk_guest cookie). This hands either to ChatService the
 * way a browser would, for this one request, so every chat endpoint - poll,
 * send, pictures, voice notes, calls - runs unchanged, on the same rows the
 * website's chat uses.
 */
class AppChatIdentity
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('sanctum')->user();
        if ($user) {
            auth('web')->setUser($user);
        } else {
            $token = (string) $request->header('X-Chat-Guest', '');
            if (preg_match('/^[a-f0-9]{40}$/', $token)) {
                $request->cookies->set(ChatService::GUEST_COOKIE, $token);
            }
        }

        return $next($request);
    }
}
