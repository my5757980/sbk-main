# Feature Specification: The SBK app — the website's frontend as a real mobile app

**Feature Branch**: `native-app`
**Created**: 2026-10-11
**Status**: Phase 1 (UI/UX) in progress
**Input**: Sir, 11 Oct 2026 (relayed by the owner), condensed:
- The app holds the website's **frontend**, the part where a customer browses and logs in. It does **not** hold the admin dashboard; staff keep managing everything from the website's admin panel.
- The **chat** comes inside the app.
- **Auction and CRM are not part of the app.** Sir connects those in the website dashboard.
- **One database:** the website's. The **UI/UX is built first**; the data is then connected to the website's database.
- The owner adds: a website and an application are different things. The whole frontend comes in, but it must look and feel like a **100% professional application**, not a web page.

## Scope

**In:** every customer-facing page of the website (`routes/front.php`), shaped as an app:

- **Home**:
  - hero slides;
  - a vehicle search with brand, model, price, year, mileage and transmission;
  - brands and the company's numbers;
  - This Week's Best Seller;
  - Why Choose SBK;
  - Member of;
  - Upcoming Collection;
  - New In-Demand Arrivals;
  - 3 easy steps;
  - search by body type;
  - client videos;
  - testimonials;
  - register call-out;
  - latest guides (blog);
  - FAQ.
- **Shop:**
  - the stock list, with search, filters and sorting;
  - vehicle detail with a photo gallery, specifications and price;
  - add to cart and wishlist;
  - related vehicles.
- **Wishlist and Cart → Checkout.**
- **Account:**
  - login, register and forgot password;
  - My Profile (edit, change password) and website orders.
- **Information:** About Us, Services, Official Partners, Blog list and article, Our Locations (offices), Bank Details, Contact Us (form), FAQ.
- **Chat:** the website's live chat — the conversation list and a conversation.

**Out (Sir):** the admin dashboard, the auction and the CRM. My Profile therefore shows website orders and the profile, not CRM invoices.

## How the app is laid out

- A bottom tab bar with five tabs: **Home · Shop · Chat · Cart · Menu**.
- The Menu tab holds the account (login, or profile and orders) and every information page.
- Native screens throughout:
  - stack navigation with native transitions;
  - pull-to-refresh where lists load;
  - skeletons while loading;
  - empty states;
  - large tap targets and safe areas.
- Light theme in the website's own colours: SBK red `#D71920`, near-black `#111216`, white cards, the logo, and the website's typography weight.

## Phases

1. **UI/UX (this phase):** every screen, built from the website's real public content (vehicles, brands, blogs, offices, bank details, FAQ, texts).
   - It is served by a data layer (`src/api`) with the same shape the website API will have.
   - Cart, wishlist and forms work inside the app.
   - Sending forms and logging in become live in phase 2.
2. **Data:** a read/write JSON API in the website (sbk-main, its own database), plus app login with a token.
   - `src/api` switches from the bundled content to the API. Nothing else in the app changes.
   - Chat connects to the website's chat.

## Acceptance (Phase 1)

- Every screen listed above opens from the tab bar or a link, with no dead end.
- Phone widths 320–430 and tablets: nothing is cut off and nothing overlaps.
- Back navigation works everywhere: Android back closes sheets first.
- An Android release APK installs and runs (package `com.sbkautotrading.app`, the SBK icon), so it updates the earlier app.
