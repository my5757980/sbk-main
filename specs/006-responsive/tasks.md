# Tasks: responsive at every width (006)

Branch `responsive` (from main 544853b). Measuring kit outside the repo: `E:\cw\sbk-resp` (scan.py, measure.js, shots.py, probe.py, compare.py).

## Measure
- [x] T1 Local copy with realistic data: test DB (staff, customers, 118 cars with photos of mixed shapes, orders, contact messages, chat) and a test CRM for the CRM records.
- [x] T2 Every page, signed in as each role: 22 public pages, 9 customer/chat pages, the desk, 36 admin pages. Each was checked at 144 widths, 320 to 2560px, and photographed at 320, 390, 768, 1024, 1366, 1920 and 2560px.

## Fix - front (public/assets/front/css/responsive.css, linked last in front/layouts/app)
- [x] T10 Hero 621-900px: the "Ready to ship" stamp sat on the car's name. It is now smaller, in the top corner.
- [x] T11 Hero 621-980px: the slide arrows sat on the heading. They are now in the photo's bottom-right corner.
- [x] T12 Car rails 621-1100px: four cards per view squeezed each card to ~150px and cut the "Add Cart" button. The rails now show two cards per view up to 860px and three up to 1100px. The button wraps under the price instead of being cut, and long tags stop before the heart.
- [x] T13 Header 981-1279px: the menu links overlapped the logo and the search box, and the wishlist/cart circles were squeezed to 23px. The links are one size smaller, the buttons keep full size, and the search is narrower. At 981-1100px the header search gives way: there is no room, the home page panel and the shop still search, and below 981px the phone menu takes over.
- [x] T14 Top bar 901-1180px: the currency box's JPY field was cut. The box now takes the room its contents need.
- [x] T15 Search panel at 1181px and up: "Any Transmission" was cut to "Transmissior".
- [x] T16 Footer:
  - 1001-1180px: "Quick link" and "Contact us" broke in two, and phone numbers ran into the next office.
  - 701-1000px: By Brands sat alone on its own row.
  - Below 1000px: the first office was indented.
- [x] T17 Product page 981-1180px: spec values ran out of their cards (four per row in the gallery column). They are now three per row.
- [x] T18 Phones (≤480px): 9-10px labels (car brand, tag, "FOB Price", partner and bank labels) are now 11px. Small links ("View Details", office phone numbers) get 30px+ tap height.

## Fix - our own views
- [x] T20 Chat widget ≤380px: the header ran to four lines. The small logo now gives way to the name.
- [x] T20b Chat box ≤340px: "Write a message…" broke onto a second line and was cut. It now says "Message…" there (ported from the WordPress chat's 1 Oct fix).
- [x] T21 Chat desk phones: the close button of the notification bar was 26px. It is now 32px.
- [x] T22 CRM record (My Invoices and admin):
  - On phones the invoice heading wraps instead of cutting "+2 more" and the date.
  - Labels are 11px.
  - Three cards per row only above 1100px (at 992-1100px "Chassis" ran out of its card).
- [x] T23 My Profile orders: long values wrap inside their cell; labels are 11px.
- [x] T24 Admin user page (users/show): the password boxes were 40px wide on phones. They now take the full width below 576px.

## Fix - admin panel (public/assets/admin/assets/css/responsive.css, linked last in admin/layouts/app)
- [x] T30 Phones: small buttons and badges (9.3-9.8px text, 20-26px tall) are now 11px and 32px tall.
- [x] T31 select2 dropdowns stay the column's width after a resize or tablet turn.
- [x] T32 The blog picture preview no longer squeezes a 4:3 photo into a square.

## Verify
- [x] T40 Every page re-scanned after the fixes (`compare.py` before vs after). Result: 265 findings gone, 0 left, 0 new, and no layout finding open on any of the 81 pages. Two of my own fixes broke a narrow range, and the re-scan caught both. The footer at 701-760px pushed the page sideways (it now uses two columns there). An admin header rule shortened "Add New" buttons (buttons are now excluded).
- [x] T41 Photographs taken again after the fixes and looked at: hero at 700/768/900px, rails at 700/860/980/1100px, header at 990-1240px, footer at 768/1024/1100px, the chat with a conversation open (customer and desk) at 320/390/768/1366px, the chat widget at 320px, the CRM invoice heading at 320px, and big screens at 1920/2560px.

## Intended (checked by eye, not faults)
- The chat button's hidden label (for screen readers), the admin template's invisible swipe strip, the page-loading bar.
- The faded "SBK" watermark behind headings; the slider's fading slides; the flip cards' hidden faces.
- Mohsin's world-clock pill (9-11px by his own clamp, his current work) and "TO SHIP" inside the 66px phone stamp.
- Carousels that show part of the next card, and wide tables that scroll sideways inside their card.
- The TinyMCE editor's own status bar (word count, "Powered by Tiny") in the admin forms, and a chat avatar's online dot on its edge.

## For Mohsin (not responsive faults)
- The 404 page is Laravel's plain default, not the site's design.
- The hero photos are 1352px wide and look soft on 1920-2560px screens. 2560px-wide versions would be sharp.
- The lorem-ipsum test product ("Optio et accusantiu") is still in the shop.
