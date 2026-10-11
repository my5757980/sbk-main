# Tasks: the SBK app

Branch `native-app`. Code in `mobile-app/`. Phase 1 is the UI/UX; phase 2 connects the website's database.

## Phase 1 — UI/UX with the website's real content

### Foundation
- [x] T01 Navigation: five tabs (Home, Shop, Chat, Cart, Menu) and a native stack for every other screen (`App.js`).
- [x] T02 Look: the website's colours, type scale, spacing and shadows; one scale for every phone width (`src/theme.js`).
- [x] T03 Shared pieces: text, buttons, pictures, badges, fields, choosers, chips, sheets, empty states, notices (`src/components/`).
- [x] T04 The only door to data (`src/api/index.js`). The shop's search, filters and sorting follow `SiteController@shop` rule for rule.
- [x] T05 The website's public content (`tools/build-content.py` → `src/api/content.json`): 118 vehicles (one lorem-ipsum test entry is left out), 4 blogs, and the filter lists.
- [x] T06 The website's fixed texts, word for word from its views (`src/api/static.js`), and its fixed pictures, phone-sized (`tools/build-assets.py` → `assets/site/`, 1.5 MB).
- [x] T07 The cart, wishlist, account, chat and shop filters, kept on the phone (`src/store/`).

### Screens
- [x] T10 Home: hero slides, stock search, brands, numbers, best sellers, why SBK, memberships and payment, upcoming, new arrivals, three steps, auction help, body types, client videos, testimonial, register, guides, FAQ, contact.
- [x] T11 Shop: search as you type, filters (brand, model, body type, price, year, mileage, transmission, grade), sort, and removable filter chips.
- [x] T12 Vehicle: swipeable photos and a full-screen gallery, facts, FOB price, every specification, description, related vehicles; wishlist, share, add to cart and checkout always in reach.
- [x] T13 Cart, Wishlist, Checkout (the website's pay-now percentage rules: 25% minimum at $3,000 or less).
- [x] T14 Account: Login, Create Account, Forgot Password, My Profile (orders, edit profile, picture, password). No CRM invoices (Sir).
- [x] T15 About, Services, Official Partners, Our Locations (local time, call, directions), Bank Details (copy each field), Contact Us, FAQ, Blog and article.
- [x] T16 Chat: the website chat's gate ("Chat with our team"), the team, the conversation (text, pictures; voice notes and calls switch on in phase 2).

### Checks
- [x] T20 Every screen photographed at 320, 390 and 430 wide and at 768 (tablet): no screen wider than the phone, no error on any screen.
- [x] T21 Release APK built (12 Oct): `com.sbkautotrading.app` 2.0.0 / code 2, 50 MB, arm64-v8a + armeabi-v7a, 16 KB aligned,
  signed with the same key as the earlier test build, so it installs over it.
- [ ] T22 Installed and opened on a real phone (the owner).

## Phase 2 — the website's database (sbk-main)

- [ ] T30 `routes/api.php` → `/api/app/*`, read: home rows, vehicles (search, filters, sort, paging), vehicle, blogs, filter lists.
- [ ] T31 Accounts: register, login (token), logout, forgot password, profile, picture, password, orders. One account for the website and the app.
- [ ] T32 Cart and checkout: send the order and pay by PayPal or card, as `checkout.store` does.
- [ ] T33 Contact form → `contact_us`, as the website's form.
- [ ] T34 Chat: the app signs in to the website's chat with its token; send, poll, pictures, voice notes, calls, push notifications.
- [ ] T35 `src/api/index.js` switches from `content.json` to the API; nothing else in the app changes.

## Acceptance (phase 1)
- Every screen opens from a tab or a link; there is no dead end; Back always goes back.
- At 320–430 wide and on a tablet, nothing is cut off and nothing overlaps.
- Where something is not connected yet (sending forms, login, chat), the screen says so in plain words; nothing pretends to have been sent.
