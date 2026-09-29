<?php

/*
 * The live chat (app/Services/Chat) - the same chat as sbkautotrading.com's,
 * running on this website and its own database.
 */
return [

    // How long since the last heartbeat still counts as "online". The website
    // beats every 10-15 s, so 45 forgives two lost beats.
    'online_seconds' => 45,

    // A call rings this long before it is a missed call.
    'ring_seconds' => 45,

    // An answered call whose other side has stopped asking about it for this
    // long is over - a closed laptop does not hang up politely.
    'call_stale_seconds' => 25,

    'max_image_bytes' => 6 * 1024 * 1024,
    'max_voice_bytes' => 8 * 1024 * 1024,

    // Pictures and voice notes: outside the public folder, served only by
    // ChatMediaController after it has checked who is asking.
    'media_dir' => storage_path('app/chat-media'),

    // Relays for calls on networks that block direct connections (optional).
    // A fixed relay such as ExpressTURN...
    'turn_urls' => env('CHAT_TURN_URLS', ''),
    'turn_user' => env('CHAT_TURN_USER', ''),
    'turn_pass' => env('CHAT_TURN_PASS', ''),
    // ...and/or Metered's short-lived credentials.
    'metered_app' => env('CHAT_METERED_APP', ''),
    'metered_key' => env('CHAT_METERED_KEY', ''),
];
