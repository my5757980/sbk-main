# Tasks: Live chat on the website

## Phase 1: Foundation

- [x] T001 Add `config/chat.php` (timings, sizes, TURN from `.env`).
- [x] T002 Migration: the seven `sbk_chat_*` tables (prefixed because the template already owns `chats`/`chat_messages`), `admins.chat_visible`, and the permissions `chat.desk` / `chat.view_all` with default grants.
- [x] T003 `ChatService`: people, guests, presence, threads, messages, calls and groups (a port of `lib.php`).

## Phase 2: US1 + US2 (customer ↔ desk)

- [x] T004 Routes + `ChatPageController`: the chat page, the three boxes (start), logout, desk and login.
- [x] T005 `ChatApiController`: poll, send, upload, name, ping and ice.
- [x] T006 `ChatMediaController`, with access checks, Range and ETag.
- [x] T007 Views: `app`, `start` and `widget`.
- [x] T008 chat.js: `window.SBK.urls`, the CSRF header, and handling a 419.
- [x] T009 Include the widget in the front and admin layouts (admin: desk staff only). Add "Live chat" to the account menu.
- [x] T010 Add "Show in the chat" to the administrators create/edit forms.

## Phase 3: US3 + US4

- [x] T011 `ChatCallController`, with SDPs read raw because TrimStrings cut the final CRLF.
- [x] T012 `ChatGroupController`.

## Phase 4: Verification

- [x] T013 HTTP suite 79/79: visitor/customer/staff/admin rules, 403/404 boundaries, media (Range/ETag), voice, calls (ring, answer, end, busy, missed), groups, widget presence, "Show in the chat", and CSRF.
- [x] T014 Two-browser Playwright suite 28/28:
  - the panel on the home page;
  - the card and badge in the admin panel;
  - text, blue ticks, the picture tray, a voice note;
  - a real WebRTC call connecting on both sides;
  - the admin drill-down;
  - the phone panel.
- [x] T015 Groups are covered by T013, and a customer gets 401.
- [x] T016 Screenshots: the admin card, the panel in a call, the admin drill-down, the phone start form, and the phone desk. No sideways scroll.
- [x] T017 Write `docs/LIVE-CHAT.md`.
- [ ] T018 After deploy: set the `CHAT_TURN_*` relay and upload limits, then re-run a real call on the live domain.
