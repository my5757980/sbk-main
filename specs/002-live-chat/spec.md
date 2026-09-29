# Feature Specification: Live chat on the website

**Feature Branch**: `live-chat`
**Created**: 2026-09-29
**Status**: In progress
**Input**: Voice note, 29 Sep 2026: "The chat you added to the main website that is running now — that same chat needs to be added to this website. My database will be connected, so don't connect the old database. Everything should remain the same from A to Z: if someone logs in from the front end of the website, they should be able to chat there. All the agents will be shown there. Don't change anything on the main website."

## What "the same chat" is

The live chat on sbkautotrading.com (WordPress, with the chat at chat.sbkautotrading.com). Its source is `MohsinRaza2000/SBK-Chat`. It has:

- **A button and panel:**
  - a round button bottom-left on every page;
  - the chat opening as a panel on the page, with an "open in a full window" button;
  - a red count of messages waiting;
  - a WhatsApp-style card when a message arrives, which rings for an incoming call;
  - desktop notifications for staff.
- **Who customers see:** the list of the team (staff/agents only, never another customer), with green dots for whoever is signed in to the website right now.
- **How a customer gets in:**
  - signed in → straight in;
  - not signed in → name, e-mail and phone once;
  - a signed-in customer with no phone is asked once;
  - a login-shaped "name" gets a "What's your name?" card.
- **Messaging:** text, emoji, links, pictures (a tray before sending, several at once, gallery viewer with swipe), voice notes, and one/two/blue ticks.
- **Calls:** voice and video between browsers (WebRTC), with ringing, answer/decline, missed calls, mute/camera, and a call line in the conversation.
- **The desk:** each agent sees their own conversations. An administrator sees every conversation as agents → that agent's customers → the chat.
- **Groups for staff:** create, rename, add/remove members, leave, and group ticks.

## User Scenarios & Testing

### US1 - A customer chats with an agent (P1)

A visitor or signed-in customer opens the button, picks an agent from "Our team" and exchanges text, pictures and voice notes. The agent answers from the admin panel's chat.

**Acceptance:**
1. Signed in on the website → the chat opens without asking who they are, unless their phone is missing.
2. Not signed in → name, e-mail and phone are required once. The same browser, or the same e-mail later, continues the same conversation.
3. The team list shows only staff with the chat permission, online first.
4. Messages and pictures arrive on the other side within about 2 seconds.
5. Ticks turn blue when the other side has the conversation open.

### US2 - The desk (P1)

**Acceptance:**
1. An agent signed in to the admin panel (or to the website with a staff role) is the desk and sees only their own conversations.
2. Roles with "See every conversation" get the drill-down agents → customers → chat.
3. The badge, card and ringing work on every page of the website and of the admin panel.

### US3 - Calls (P2)

Voice and video work in both directions. A call rings for 45 s and then becomes "Missed". A call already in progress blocks a second one ("busy").

### US4 - Groups (P2)

Staff-only rooms. Customers can never reach them (401).

### Edge Cases

- **Which account wins when a browser is signed in twice:** if both a customer and a staff account are signed in, staff wins, as on the old site.
- **Old chats are not brought over:** they are in the old, separate database, which Sir said not to connect.
- **Media:** stored outside `public/` and served only after the same access check. Guessed ids return 404.
- **Removed or demoted staff:** a person whose role loses the chat permission loses the desk on their next request.

## Requirements

- **FR-001**: All data lives in the website's own database, through Laravel migrations. Tables: `sbk_chat_guests`, `sbk_chat_threads`, `sbk_chat_messages`, `sbk_chat_presence`, `sbk_chat_calls`, `sbk_chat_groups`, `sbk_chat_group_members`.
- **FR-002**:
  - "Staff" means an active admin-panel account whose role has **Live Chat → "Chat desk"** (`chat.desk`), or the system administrator.
  - Customers see staff who are "listed". A per-person **"Show in the chat"** setting (role / always / never) exists, as on the old site. The system administrator is hidden unless set to "always".
- **FR-003**: **"See every conversation"** (`chat.view_all`) gives the administrator drill-down.
- **FR-004**:
  - The migration ticks `chat.desk` for the slugs super-admin, admin, administrator, manager, csd and agent.
  - It ticks `chat.view_all` for super-admin, admin and administrator.
- **FR-005**: The UI is the old chat's CSS and JS. The only JS changes are the addresses (Laravel routes) and the CSRF token on POSTs.
- **FR-006**: Nothing on sbkautotrading.com, chat.sbkautotrading.com, the auction portal or the old app changes.

## Success Criteria

- **SC-001**: A two-browser test passes: customer ↔ agent text, picture, voice note, ticks, unread badge, and admin drill-down.
- **SC-002**: The call handshake test passes: start, ring, answer, end, missed after 45 s, busy.
- **SC-003**: Group create, message, rename, add, remove and leave all work. A customer gets 401 on every group request.
- **SC-004**: Every chat page works at 320–1440 px, including inside the website's panel.
