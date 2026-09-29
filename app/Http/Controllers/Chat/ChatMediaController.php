<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Services\Chat\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Hand over a picture or a voice note - to the people entitled to it (media.php
 * of the SBK chat). The files are outside the public folder; a picture in a
 * conversation belongs to its customer and member of staff, one in a group to
 * the group's members. Anyone else, or a guessed id, gets 404.
 */
class ChatMediaController extends Controller
{
    /* The type a file goes out under. finfo names an Android voice note
       video/mp4 or audio/x-m4a and a browser's WebM video/webm; all of them are
       only sound, and Safari will not guess. */
    private const SERVED = [
        'image/jpeg' => 'image/jpeg', 'image/png' => 'image/png',
        'image/gif' => 'image/gif', 'image/webp' => 'image/webp',
        'audio/webm' => 'audio/webm', 'video/webm' => 'audio/webm',
        'audio/ogg' => 'audio/ogg', 'audio/mpeg' => 'audio/mpeg',
        'audio/mp4' => 'audio/mp4', 'audio/x-m4a' => 'audio/mp4', 'video/mp4' => 'audio/mp4',
        'audio/aac' => 'audio/aac', 'audio/wav' => 'audio/wav', 'audio/x-wav' => 'audio/wav',
        'video/3gpp' => 'audio/3gpp', 'audio/3gpp' => 'audio/3gpp', 'audio/amr' => 'audio/amr',
    ];

    public function show(Request $request, int $message, ChatService $chat)
    {
        $m = DB::table('sbk_chat_messages')->where('id', $message)
            ->first(['id', 'thread_id', 'group_id', 'media_path', 'media_mime']);

        $allowed = false;
        if ($m && !empty($m->media_path)) {
            $allowed = $m->group_id
                ? (bool) $chat->mayReadGroup((int) $m->group_id)
                : (bool) $chat->mayReadThread((int) $m->thread_id);
        }
        abort_unless($allowed, 404);

        // The path came out of our own table, but it is still checked against its folder.
        $root = realpath(ChatService::mediaDir());
        $real = realpath(ChatService::mediaDir() . '/' . $m->media_path);
        abort_unless($root !== false && $real !== false && str_starts_with($real, $root . DIRECTORY_SEPARATOR), 404);

        // Symfony answers If-None-Match (304) and one byte Range (206/416) itself.
        $response = new BinaryFileResponse($real, 200, [
            'Content-Type' => self::SERVED[(string) $m->media_mime] ?? 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ], false, ResponseHeaderBag::DISPOSITION_INLINE, false, false);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE,
            'sbk-' . (int) $m->id . '.' . pathinfo($real, PATHINFO_EXTENSION));
        $response->setEtag(md5($m->media_path . '|' . filesize($real)));
        $response->setPrivate();
        $response->setMaxAge(604800);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->isNotModified($request);

        return $response;
    }
}
