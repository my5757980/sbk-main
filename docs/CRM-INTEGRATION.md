# SBK website ↔ SBK CRM

This website and the SBK CRM (crmglobal.sbkautotrading.com) work together in three ways:

1. **"CRM Global" button** (admin panel top bar). Someone logged in to this admin panel lands in the CRM **already signed in**, as long as the CRM has an active user with the same email. Otherwise the CRM login page opens with a short note.
2. **Admin → Users.** Every CRM customer (anyone with an invoice and an email in the CRM) gets a website account automatically, marked **CRM**. The user page shows their complete CRM record:
   - invoices, prices, advance, paid and remaining amounts;
   - payment history;
   - car details, car photos (with a full-size viewer) and documents by folder;
   - the invoice PDF.
3. **My Profile → My Invoices.** The customer sees the same record (minus rejected invoices) after logging in.

The CRM stays the single source of truth. Records are read live (cached for 60 s), so nothing has to be copied and nothing goes out of date. New customers get an account within 5 minutes.

## Setup (production)

1. **`.env`**:
   ```
   CRM_URL=https://crmglobal.sbkautotrading.com
   CRM_PRIVATE_KEY=<one line - base64 of the PEM, supplied separately>
   ```
   Or use `CRM_PRIVATE_KEY_PATH=/path/outside/public_html/crm-private-key.pem`. The key is **secret**: never commit it. The CRM holds only the matching public key.
2. **Migration:** `php artisan migrate`. It adds `crm_email`, `crm_source`, `crm_pending_email`, `crm_summary` and `crm_synced_at` to `users`.
3. **Cron** (for the 5-minute account sync):
   ```
   * * * * * cd /path/to/site && php artisan schedule:run >> /dev/null 2>&1
   ```
   Without the cron, opening Admin → Users (at most every 5 min) or pressing **Sync from CRM** does the same job.
4. **Customer passwords.** Each CRM customer gets a **separate** random password (like `Kp7mQ-x3RtA`) for the website login:
   - **Admin → Users → Generate passwords (N)** covers every CRM customer who has none yet. It shows the list **once**, with a *Copy all* button, so staff can send each customer their own email and password.
   - **Generate password** on a customer's page gives one customer a new password, also shown once.
   - Only the hash is stored. Pressing the button again never touches passwords already handed out or set by the customer.
   - With **Mail** (`MAIL_*` in `.env`) set up, customers can also use **Forgot password**, or staff can press **Send password setup link**.

**Check it:** run `php artisan crm:check`. It tests the key, the database columns and the connection, then syncs, and says exactly what to fix if something is missing. Admin → Users shows the same problem as a red note. `php artisan crm:sync-customers` only runs the sync.

**No CRM data showing?** Almost always one of these:
- `CRM_PRIVATE_KEY` is not in `.env`. Add it, then run `php artisan config:clear`.
- The migration has not run.
- On Windows/XAMPP, PHP lacks the CA certificate bundle (cURL error 60).

## How accounts are linked (security)
- If no website account has the customer's email, one is **created**: role customer, random password.
- An account the sync created, or one whose email was proved by completing a password reset, is **linked**.
- Any other existing account with that email (for example a public sign-up) is **not** linked. It shows **Pending link** until its owner completes "Forgot password", or an admin presses **Link now**. Otherwise anyone could register with a customer's email and see their invoices.
- An account already linked to one CRM customer is never re-pointed to another. Changing a profile email resets its verification.
- Photos, documents and PDFs are served by this website only after checking that they belong to the record being viewed. Guessed ids return 404. Files are cached in `storage/app/crm-cache`.
- Internal CRM figures are never sent to the website: push price, rates, costs, profit, receipts and rejection notes.

## Pieces
| Where | What |
|---|---|
| `app/Services/Crm/CrmSigner.php` | RSA-SHA256 tokens: SSO (60 s, single use) and API reads (bound to method, path and query) |
| `app/Services/Crm/CrmClient.php` | Signed reads of the CRM's `/api/external/*`, cached, failures degrade gracefully |
| `app/Services/Crm/CrmCustomerSync.php` + `crm:sync-customers` | Account creation and linking |
| `app/Services/Crm/CrmRecord.php`, `CrmFileCache.php` | Visibility rules, ownership checks, file cache |
| `app/Http/Controllers/Admin/CrmController.php` | CRM Global, Sync, Link now, Send password link, admin file/PDF routes |
| `app/Http/Controllers/Front/CrmRecordController.php`, `PasswordResetController.php` | Customer file/PDF routes, forgot/reset password |
| `resources/views/partials/crm/record.blade.php` | The record UI used in both admin and profile |

The CRM side lives in the `official-crm` repository: `src/lib/website-link.ts`, `src/app/api/auth/sso` and `src/app/api/external/*`.
