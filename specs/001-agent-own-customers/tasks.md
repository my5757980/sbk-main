# Tasks: Agents see only their own customers

**Input**: [spec.md](spec.md), [plan.md](plan.md)

## Phase 1: CRM feed

- [x] T001 Add `agentEmails` and `agentName` to `src/app/api/external/customers/route.ts` in both CRM repos (identical file).
- [x] T002 Test the real SQL on Postgres (PGlite) with CRM table shapes: one owner, two owners, consignee-email customer, deleted owner, rejected-only customer, and old columns byte-identical. Result: 10/10.

## Phase 2: Website foundation

- [x] T003 Add the `users.view_all` permission to `config/admin_permissions.php`.
- [x] T004 Migration: create `customer_agents`, insert the permission if it is missing, and apply the default grants by slug.
- [x] T005 Add `Admin::canSeeAllCustomers()`, `User::scopeVisibleTo()` and `User::agents()`.

## Phase 3: US1 Agent sees own customers

- [x] T006 CrmCustomerSync stores the `crm` ownership rows, and never wipes them when the CRM does not send `agentEmails`.
- [x] T007 UserController scopes index, show, edit, update, destroy, changePassword and updateStatus. Also: the agent form offers Customer only, updateStatus writes `status` only, and a panel-created customer gets an owner row.
- [x] T008 CrmController scopes link, generatePasswords, file and pdf.
- [x] T009 DashboardController scopes the Users count and the latest users.

## Phase 4: US2 See-all roles

- [x] T010 Add the Agent column in Users for see-all admins.

## Phase 5: Verification

- [x] T011 Local HTTP test (php artisan serve, SQLite), with 2 agents, a manager, a CSD and the system admin:
  - each agent's list and dashboard;
  - 404 on the other agent's customer, for every per-user route;
  - generate passwords scoped;
  - status switch limited to `status`;
  - the agent cannot pick a staff role;
  - see-all roles list everyone, with the Agent column.
- [ ] T012 After deploy: the CRM answers `agentEmails` on the live host, and a live agent sees only their customers.
