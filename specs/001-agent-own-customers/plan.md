# Implementation Plan: Agents see only their own customers

**Branch**: `agent-own-customers` | **Date**: 2026-09-29 | **Spec**: [spec.md](spec.md)

## Summary

"Whose customer is this" does not exist on the website today. The CRM already answers it with `leads.created_by`, which is how the CRM limits its own agents. Two changes follow from that:

- The CRM feed now carries the owner's email.
- The website keeps one small ownership table and filters every Users screen by it. A see-all permission switches the filter off.

## Technical Context

- **Website:** Laravel 10, PHP 8.1+. MySQL in production; SQLite is used for local tests.
- **Admin auth:** the `admin` guard, with roles and permissions in `admin_roles`, `admin_permissions` and `admin_permission_admin_role`. The `admin.permission` middleware maps route names to permissions.
- **CRM:** Next.js on Neon Postgres. `/api/external/customers` is signed by the website's RSA key.

## Key Decisions

| Decision | Chosen | Rejected, and why |
|---|---|---|
| Source of ownership | CRM `leads.created_by` → `users.email` | The invoice `salesperson` text is a free name and matches badly. A manual-only website field would duplicate the CRM and drift. |
| Match CRM agent → website admin | Email, lower-cased | By id is impossible: the two apps have separate user tables. The CRM Global button already relies on email. |
| Store ownership | `customer_agents` table (user_id, agent_email, source) | A JSON column needs a LIKE match, which is unsafe with `_`/`%` in emails and not portable across MySQL and SQLite. A single column drops the second agent. |
| See-all switch | Permission `users.view_all` on the role | A hard-coded role list: Sir asked for roles and permissions. |
| Default grants | By slug: super-admin, admin, administrator, manager, csd | Nobody ticked: managers would suddenly see an empty list after deploy. |

## Changes

**CRM** (both `MohsinRaza2000/official-crm` and `my5757980/office-crm`, same file):
- `src/app/api/external/customers/route.ts`: `LEFT JOIN users u ON u.id = l.created_by`, adding `agentEmails` and `agentName`.

**Website**:
- `config/admin_permissions.php`: `users.view_all` → "See all customers".
- `database/migrations/2026_09_29_000001_create_customer_agents_table.php`: creates the table, inserts the permission if it is missing, and applies the default grants.
- `app/Models/Admin.php`: `canSeeAllCustomers()`.
- `app/Models/User.php`: `scopeVisibleTo(Admin)` and `agents()` (the ownership rows).
- `app/Services/Crm/CrmCustomerSync.php`: stores the `crm` ownership rows when `agentEmails` is present.
- `app/Http/Controllers/Admin/UserController.php`: scopes every action. An agent's form offers only Customer. The status switch is limited to `status`. A panel-created customer gets an owner row.
- `app/Http/Controllers/Admin/CrmController.php`: scopes link, generate passwords, and file/PDF.
- `app/Http/Controllers/Admin/DashboardController.php`: scopes the Users count and the latest users.
- `resources/views/admin/users/index.blade.php`: adds an Agent column for see-all admins.

## Risks

1. **Website email ≠ CRM email for an agent.** That agent then sees only panel-created customers. Mitigation: the Agent column shows Sir the match, and the fix is to make the emails equal.
2. **A role slug that differs from the defaults**, e.g. "Sales Manager". Sir ticks the permission once in Roles & Permissions.
3. **The CRM is deployed after the website.** Ownership rows are empty until the CRM answers with `agentEmails`, so agents see only panel-created customers until then. Deploy the CRM first.
