# Implementation Plan: Live chat on the website

**Branch**: `live-chat` | **Spec**: [spec.md](spec.md)

## Summary

The old chat is plain PHP with two databases (the auction DB for chat tables, WordPress for people) and a signed handover between two domains. The port keeps the browser half as it is and moves the server half into Laravel:

- **Browser half (kept):** chat.css and chat.js, the pages' markup, and the website button, panel and card.
- **Server half (moved):**
  - one database (this website's);
  - people come from `users` (customers) and `admins` (staff);
  - no handover is needed, because the chat is on the same domain, so the session already says who is here.

## Key Decisions

| Decision | Chosen | Why / rejected |
|---|---|---|
| Server code | `ChatService` ports `lib.php` function by function, with controllers for the endpoints | A rewrite in another shape would drift from behaviour that was proven in 50+ live tests. Embedding the plain PHP files would need a second DB connection and the old cookies and handover. |
| Addresses | Clean Laravel routes under `/chat` (`/chat/api/poll`…), injected into chat.js through `window.SBK.urls` | Keeping `.php` paths breaks on nginx's `\.php$` blocks. |
| Staff identity | `admins.id`, with roles and permissions from Roles & Permissions | Sir's panel is where agents are made. A staff member signed in on the front is mapped to their admin account by e-mail. |
| Who is staff | The permissions `chat.desk` / `chat.view_all`, plus the per-person `admins.chat_visible` | Sir asked for roles and permissions. This is the same shape as the old STAFF_ROLES list plus its tick box. |
| Time | PHP writes UTC `Y-m-d H:i:s` into DATETIME columns and compares against PHP-computed cutoffs | chat.js reads times as UTC. It also works the same on MySQL (production) and SQLite (tests). |
| SQL | Query builder: `upsert` / `insertOrIgnore` / `CASE` in place of `ON DUPLICATE KEY` / `INSERT IGNORE` / `GREATEST` | Portable. The behaviour is the same. |
| Media | `storage/app/chat-media/ab/cd/<random>.<ext>`, served by a controller that checks access first, with Range and ETag support | Same as before: outside the web root. |
| CSRF | chat.js sends `X-CSRF-TOKEN` on POSTs, and `_token` in the beacon | Laravel's protection stays on for the chat. |
| Push (FCM) and the app pass | Not ported | There is no app for this website yet. Task 3's app is a WebView, where the session cookie works. |

## Files

- `config/chat.php`
- `database/migrations/2026_09_29_000002_create_chat_tables.php`
- `config/admin_permissions.php`: a `chat` module.
- `app/Services/Chat/ChatService.php`
- `app/Http/Controllers/Chat/*`: Page, Api, Call, Group, Media.
- `routes/chat.php`, plus `RouteServiceProvider::mapChatRoutes()`.
- `resources/views/chat/{app,start,widget}.blade.php`
- `public/assets/chat/{chat.css,chat.js,img/*}`
- Front + admin layouts `@include('chat.widget')`. "Live chat" in the account menu.
- `Admin::$fillable` gets `chat_visible`. The administrators create/edit forms get "Show in the chat".
- `docs/LIVE-CHAT.md`

## Risks

1. **Each poll boots Laravel**, which costs more than plain PHP. The poll interval is unchanged (1.5 s, 6 s when hidden). Watch the host's process limit.
2. **TURN relay:** without `CHAT_TURN_*` in `.env`, about 2 calls in 10 on strict networks will not connect. STUN still works.
3. **Upload limits:** `upload_max_filesize` must be at least 8M on the server for voice notes.
