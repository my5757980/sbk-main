# Live chat

The same live chat as on sbkautotrading.com, running on this website and **its own database**.
It has the round button (bottom left, every page), the chat panel, the red count, the pop-up card, voice notes, pictures, voice and video calls, groups for staff, and the administrator's agents → customers → chat view.

## Who is who

| Person | Where they come from | What they see |
|---|---|---|
| **Visitor** | Not signed in. Gives name, e-mail and phone once; a cookie keeps them. | "Our team": staff only, online first |
| **Customer** | Signed in on the website (`users`, role customer). Goes straight in; asked for a phone once if it is missing. | "Our team" |
| **Staff (the desk)** | An active admin-panel account (`admins`) whose role has **Live Chat → Chat desk**. A staff user signed in on the front is matched to their admin account by e-mail. | Their own conversations |
| **Sees everything** | Role with **Live Chat → See every conversation**, or the system administrator. | Agents → that agent's customers → the chat |

- **Show in the chat.** Admin panel → Administrators → edit → **Show in the chat** (role / always / never) decides who customers see.
- **The system administrator** is hidden from customers unless set to "Always show", as on the old site. They still see every conversation.
- **Staff groups:** staff can make groups (**+** beside the list). Membership only; customers can never see them.

## Setup (production)

1. **Migrate:** `php artisan migrate`.
   - It creates `sbk_chat_guests`, `sbk_chat_threads`, `sbk_chat_messages`, `sbk_chat_presence`, `sbk_chat_calls`, `sbk_chat_groups` and `sbk_chat_group_members`.
   - It adds `admins.chat_visible` and the permissions `chat.desk` / `chat.view_all`.
   - "Chat desk" is ticked for the role slugs super-admin, admin, administrator, manager, csd and agent. "See every conversation" is ticked for super-admin, admin and administrator.
   - Change either under Roles & Permissions.
   - The tables are prefixed `sbk_chat_` because the original template already has `chats` / `chat_messages` (unused). Those are left untouched.
2. **Storage:** `storage/app/chat-media` must be writable. Pictures and voice notes live there, outside `public/`, and are served only after an access check.
3. **PHP upload limits:** set `upload_max_filesize` and `post_max_size` to at least **8M** for voice notes. Pictures are capped at 6 MB and re-encoded down to 1600 px.
4. **Calls:** voice/video are browser to browser (WebRTC) and connect directly for about 8 in 10 people. For the rest (strict mobile or office networks), add a free relay to `.env`:
   ```
   CHAT_TURN_URLS=turn:<relay-host>:3478,turn:<relay-host>:3478?transport=tcp
   CHAT_TURN_USER=<user>
   CHAT_TURN_PASS=<password>
   ```
   Metered is also supported (`CHAT_METERED_APP`, `CHAT_METERED_KEY`). The relay's password never reaches the browser.
5. **HTTPS:** the site must be on HTTPS in production. Browsers only allow the microphone and camera (voice notes and calls) on HTTPS.

## How it works

| Where | What |
|---|---|
| `app/Services/Chat/ChatService.php` | People, customers, presence, threads, messages, calls, groups. A function-by-function port of the old chat's `lib.php`. |
| `app/Http/Controllers/Chat/*` | Page (chat page, the three boxes, leave, desk), Api (poll, send, upload, name, ping, ice), Call, Group, Media |
| `routes/chat.php` | Everything under `/chat` |
| `resources/views/chat/app.blade.php` | The chat page (it hands chat.js its addresses and CSRF token) |
| `resources/views/chat/start.blade.php` | Name / e-mail / telephone |
| `resources/views/chat/widget.blade.php` | The button, the panel, the card and the heartbeat. It is included by the front layout on every page, and by the admin layout for desk staff only. |
| `public/assets/chat/` | The old chat's own CSS and JS. The JS changes only its addresses and the CSRF header. |

**Timing:**
- An open chat polls every 1.5 s (6 s when the tab is hidden).
- Every website page pings every 10 s. That is what makes staff "online" while they are signed in.
- A call rings for 45 s before it is missed.

## Notes for developers

- **Times** are written by PHP as UTC `Y-m-d H:i:s` into DATETIME columns. chat.js reads them as UTC.
- **Call SDPs** (`offer` / `answer`) are read from the raw request body. Laravel's `TrimStrings` would cut their final CRLF, and browsers then refuse the call.
- **Old conversations** on the old site's database are not carried over. That database is not connected, by design.
