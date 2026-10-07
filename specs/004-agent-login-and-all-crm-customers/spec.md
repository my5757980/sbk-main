# Feature Specification: Staff made in Users can sign in; agents see every CRM customer

**Feature Branch**: `agent-login-fix`
**Created**: 2026-10-07
**Status**: Implemented (awaiting push and deploy)
**Input**: Sir, 7 Oct 2026 (summarised): "On the live site it works, but the agent sees only some of their customers. In the updated Git version, when I create a user as an agent or a manager in Users and set a password, that agent cannot log in to the admin panel. First make it log in. Then, for example, Talal's customers should all show on his dashboard; match it with the CRM. And when we change a customer's password, they log in on the website and see all their invoices. Keep the customer detail page (invoices and payments from the CRM) as it is."

## Part 1 - A staff member made in Users can sign in (P1)

**What was found.** Seven situations were reproduced on MariaDB 10.11 (strict mode, like the live MySQL) and on SQLite. A plain agent or manager signed in fine on both. These situations broke it:

1. **Long phone or name.** The phone (or a name) was longer than the admin account's column (phone 24, names 32). MySQL refuses the value, so creating the user and every later sign-in ended in a server error. SQLite never shows this.
2. **Renamed role.** Renaming a role also changed its slug, and every staff Users row points at that slug. On the next save of such a person, the edit form had no matching role and silently chose "Customer". The person became a customer and the admin login closed.
3. **Main administrator's email.** Giving a new user the main administrator's own email left that login on the administrator's password, so the password typed in Users never worked.
4. **Role without Dashboard.** A role with no Dashboard permission landed on a 403 page straight after signing in.
5. **Switched off.** Switching a staff member off in the Users list left their admin login open.
6. **Deleted.** Deleting a staff member in Users left their admin login open.
7. **Login messages.** The login page only flashed a generic 2-second popup.

**Requirements**

- **FR-001** The admin account copy fits its columns: names are cut to 32 characters and the phone to 24 (`Admin::profileFrom`).
- **FR-002** Renaming a role keeps its slug.
  - A Users row left on an old slug is put right from its admin account on sign-in (`Admin::roleFor`).
  - When the role no longer exists, the edit form shows "- choose a role -" instead of choosing Customer.
- **FR-003** A new user, or an email change, that gives a staff role to the main administrator's email is refused, with the reason shown.
- **FR-004** A role with no section ticked lands on its own profile, not on a 403.
- **FR-005** Switching a staff member off in Users closes their admin login, and switching them on opens it again. Deleting them closes it.
- **FR-006** The login page keeps its message on the page. A wrong password says only "These credentials do not match our records." With the right password, it also says what is in the way: switched off, a customer account, a role that no longer exists, or the main administrator's email.

## Part 2 - An agent sees every CRM customer of theirs (P1)

**What was found.** On 2 Oct the website already showed 100% of what the CRM sends: every agent's customer count matched the feed. The gap is the feed itself. It sends only invoices with a customer email, and an email has been optional on leads and invoice requests. A customer with no email therefore never reached the website, nor their agent.

**Requirements**

- **FR-007** The CRM feed (`/api/external/customers`) also sends `noEmailCustomers`: one entry per CRM lead that has non-rejected invoices with no customer email, with its agent.
  - Every non-rejected invoice is in exactly one of the two lists.
  - `/api/external/customer?lead=<id>` gives that lead's record.
- **FR-008** The 5-minute sync keeps them in `crm_leads` (CrmLead). An older CRM that does not send the list leaves the table as it is.
  - No website account is made for them, because a login needs an email.
  - When the CRM gets an email for one of them, the ordinary sync makes their account with all their invoices, and they leave the list.
- **FR-009** Admin Users has a section "CRM customers without an email (N)".
  - An agent sees only their own customers there. "See all customers" sees everyone, with an Agent column.
  - Each customer opens their full CRM record (invoices, payments, photos, documents, PDF) through the same view the user page uses.
  - Another agent's customer answers 404.
- **FR-010** The dashboard "Customers" number includes them and says how many have no email.

## Part 3 - The customer sees all their invoices (P1)

- **FR-011** Staff setting a customer's password links an account that was waiting on that very email, the same as "Link now". The customer then sees their invoices as soon as they sign in.
- **FR-012** An invoice with several vehicles (CRM, 5 Oct) shows "+N more" and every vehicle with its own C&F price. This applies to the admin and to the customer's My Invoices. On a phone it is a list, not a wide table.
- The admin user detail page and its CRM record keep working as before; they gain only the vehicles list.

## Tests

- **`suite.py`: 20/20 on MariaDB, 20/20 on SQLite.** Covers new agent and manager sign-in, plus every situation in Part 1 (long phone, main administrator's email, renamed role, stale slug, role without Dashboard, switch off and on, inactive, deleted, customer, wrong password).
- **`allcust.py`: 32/32.** The website on MariaDB reads the REAL CRM route code (`crm-server.ts`, PGlite, a throwaway key pair). It covers:
  - the sync counts and `crm_leads` with each agent;
  - each agent seeing only their own customers, and the manager seeing all;
  - the record, photo and PDF of a no-email customer, and 404s for another agent's;
  - the dashboard number;
  - a 3-vehicle invoice;
  - a password set by staff, then the customer signing in and seeing everything (and not the rejected invoice);
  - a waiting account linked by setting its password;
  - an email added in the CRM turning the customer into an account.
- **CRM `run-feed.ts`: 24/24.** The partition of invoices into the two lists, the record by lead, 400/401, and that a signature for one lead does not open another.
- **Screenshots** at 1366 and 390 px: nothing reaches past the screen.

## Deploy

1. Deploy the CRM first. The extra list is ignored by the website until step 2.
2. Deploy the website, then run `php artisan migrate`. This adds the new `crm_leads` table and changes nothing that exists.
