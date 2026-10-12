<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Services\Chat\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * A call's handshake - start, answer, decline, cancel, end, and "where is it"
 * (api/call.php of the SBK chat). The call itself is WebRTC, browser to
 * browser; only the offer, the answer and the state come here. Every action
 * checks that the person asking is one of the two people in the call.
 */
class ChatCallController extends Controller
{
    public function handle(Request $request, ChatService $chat)
    {
        $g = $chat->guest();
        $s = $chat->staff();
        if (!$g && !$s) {
            return ChatService::json(['ok' => false, 'signedout' => true], 401);
        }
        ChatService::refuseUntilDetails($g, $s);
        $viewerIsGuest = $g && !$s;
        $me = $viewerIsGuest ? $g : $s;
        $isPost = $request->isMethod('post');

        $action = (string) $request->input('action', '');
        $chat->sweepCalls();

        if ($action === 'start') {
            if (!$isPost) {
                return ChatService::json(['ok' => false, 'error' => 'POST only'], 405);
            }

            return $this->start($request, $chat, $viewerIsGuest, $g, $s, $me);
        }

        // Everything below is about one call that already exists.
        $call = $chat->callRow((int) $request->input('call', 0));
        if (!$call || !ChatService::isCallParty($call, $viewerIsGuest, (int) $me['id'])) {
            return ChatService::json(['ok' => false, 'error' => 'That call is not yours.'], 403);
        }
        $incoming = ChatService::callIsIncoming($call, $viewerIsGuest);

        if ($action === 'state') {
            $chat->touchCall($call, $viewerIsGuest);
            $out = $chat->callOut($call, $viewerIsGuest);
            // Each side is handed only the half of the handshake it needs.
            if ($incoming && $call['state'] === 'ringing') {
                $out['offer'] = (string) $call['offer'];
            }
            if (!$incoming && $call['state'] === 'accepted') {
                $out['answer'] = (string) $call['answer'];
            }

            return ChatService::json(['ok' => true, 'call' => $out]);
        }

        if (!$isPost) {
            return ChatService::json(['ok' => false, 'error' => 'POST only'], 405);
        }

        if ($action === 'answer') {
            if (!$incoming || $call['state'] !== 'ringing') {
                return ChatService::json(['ok' => false, 'error' => 'That call is no longer ringing.'], 409);
            }
            $answer = self::sdp($request, 'answer');
            if (strlen($answer) < 50 || strlen($answer) > 200000 || !str_contains($answer, 'v=0')) {
                return ChatService::json(['ok' => false, 'error' => 'That call could not be answered.'], 400);
            }
            $now = ChatService::now();
            $ok = DB::table('sbk_chat_calls')->where('id', $call['id'])->where('state', 'ringing')->update([
                'state' => 'accepted', 'answer' => $answer, 'answered_at' => $now,
                'guest_seen' => $now, 'staff_seen' => $now,
            ]);
            if (!$ok) {
                return ChatService::json(['ok' => false, 'error' => 'That call is no longer ringing.'], 409);
            }

            return ChatService::json(['ok' => true, 'call' => $chat->callOut($chat->callRow((int) $call['id']), $viewerIsGuest)]);
        }

        if (in_array($action, ['decline', 'cancel', 'end'], true)) {
            if ($call['state'] === 'ringing') {
                // Not yet answered: the one ringing declines, the one calling cancels.
                $chat->finishCall($call, $incoming ? 'declined' : 'cancelled');
            } elseif ($call['state'] === 'accepted') {
                $chat->finishCall($call, $request->input('failed') ? 'failed' : 'ended');
            }

            return ChatService::json(['ok' => true, 'call' => $chat->callOut($chat->callRow((int) $call['id']), $viewerIsGuest)]);
        }

        return ChatService::json(['ok' => false, 'error' => 'Unknown action.'], 400);
    }

    /**
     * A session description exactly as the browser sent it. Laravel's TrimStrings
     * middleware cuts the final CRLF off every input, and a browser refuses an
     * SDP without it ("could not connect" after the answer). So it is read from
     * the raw form body, as the old chat read $_POST.
     */
    private static function sdp(Request $request, string $name): string
    {
        parse_str((string) $request->getContent(), $raw);
        $value = $raw[$name] ?? $request->input($name, '');

        return is_string($value) ? $value : '';
    }

    private function start(Request $request, ChatService $chat, bool $viewerIsGuest, ?array $g, ?array $s, array $me)
    {
        $kind = $request->input('kind') === 'video' ? 'video' : 'voice';
        $offer = self::sdp($request, 'offer');
        if (strlen($offer) < 50 || strlen($offer) > 200000 || !str_contains($offer, 'v=0')) {
            return ChatService::json(['ok' => false, 'error' => 'That call could not be set up.'], 400);
        }

        if ($viewerIsGuest) {
            $to = $chat->person((int) $request->input('to', 0));
            if (!$to || !$to['is_listed']) {
                return ChatService::json(['ok' => false, 'error' => 'That person is not available.'], 400);
            }
            $t = $chat->threadFor((int) $g['id'], (int) $to['id']);
        } else {
            $t = $chat->mayReadThread((int) $request->input('thread', 0));
            /* Only the member of staff the conversation belongs to rings the
               customer - reading someone else's conversation is not a phone. */
            if (!$t || (int) $t['staff_id'] !== (int) $s['id']) {
                return ChatService::json(['ok' => false, 'error' => 'That conversation is not yours.'], 403);
            }
        }

        // One call at a time, for both people.
        if ($chat->liveCallFor($viewerIsGuest, (int) $me['id'])) {
            return ChatService::json(['ok' => false, 'error' => 'You are already in a call.'], 409);
        }
        $calleeBusy = $viewerIsGuest ? $chat->liveCallFor(false, (int) $t['staff_id'])
                                     : $chat->liveCallFor(true, (int) $t['guest_id']);
        if ($calleeBusy) {
            return ChatService::json(['ok' => false, 'busy' => true, 'error' => 'They are on another call. Try again in a minute.'], 409);
        }

        $now = ChatService::now();
        $id = (int) DB::table('sbk_chat_calls')->insertGetId([
            'thread_id' => (int) $t['id'],
            'from_guest' => $viewerIsGuest ? 1 : 0,
            'kind' => $kind,
            'offer' => $offer,
            'state' => 'ringing',
            'created_at' => $now,
            'guest_seen' => $now,
            'staff_seen' => $now,
        ]);

        // A customer rung by the desk hears it on their phone (the SBK app).
        \App\Services\App\AppPush::ringing($id);

        return ChatService::json(['ok' => true, 'call' => $chat->callOut($chat->callRow($id), $viewerIsGuest)]);
    }
}
