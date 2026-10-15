# Spec: the website responsive at every width (006)

Owner's ask (13 Oct 2026): scan Mohsin's custom website deeply, from the smallest phone to the largest desktop screen, look at it (not by guess), and make it 100% responsive - the whole website, the live chat on its database, and the CRM data shown on it. The CRM itself and the auction are out of scope.

## In scope
- Every public page: home, about, partners, shop (with filters), product, services, blog and article, locations, bank, contact, login, register, forgot/reset password, cart, checkout, wishlist, 404.
- The customer's account: My Profile tabs (Invoices from the CRM, Orders, Profile), cart and checkout signed in.
- The live chat: the widget on every page, the chat start page, the customer's chat, the staff desk with a conversation open.
- The website's admin panel (as staff use it): dashboard, products, brands, models, customers (with their CRM record, the email-less CRM customers), orders, blogs, contact messages, FAQs, pages, roles, administrators, site settings, profile; its login and password pages.

## Out of scope
- The CRM application and the auction portal (owner: already responsive).
- Changing content, colours or the design language; only layout at widths where it breaks.

## What "responsive" means here (acceptance)
At every width from 320px to 2560px (checked in 16-40px steps plus every CSS breakpoint and the pixel after it):
- [ ] The page never scrolls sideways.
- [ ] Nothing sticks out past the screen edge unless it is inside something that scrolls (a carousel, a wide table in its scroller).
- [ ] No button, link or text is cut off by its own card or box.
- [ ] No text sits on top of other text.
- [ ] No picture is stretched out of shape.
- [ ] On phones (up to 480px): no text under 11px and no standalone button or link under 28px tall, where that can be changed without changing the design.
- [ ] Judged by eye too: photographs at 320, 390, 768, 1024, 1366, 1920 and 2560px of every page look intended.

## Constraints
- Mohsin is editing the same files (`home.css`, header, footer, shop, welcome) every day, so fixes for his pages go in one new stylesheet loaded after his, not into his files.
- Our own views (CRM record, profile, chat) are fixed in place.
- No content, colours or design changes beyond what a width needs.
