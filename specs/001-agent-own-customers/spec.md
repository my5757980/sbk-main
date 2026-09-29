# Feature Specification: Agents see only their own customers

**Feature Branch**: `agent-own-customers`
**Created**: 2026-09-29
**Status**: Implemented (awaiting deploy)
**Input**: Sir, 29 Sep 2026: "If I log in with any agent, it's showing all users to everyone. The users that belong to Sonia should be visible only to Sonia. Same for every agent. Manager and CSD should be able to see everyone's users. Set it by roles and permissions; if I want someone to see all users, I'll just change their role. I'm talking about the website's dashboard."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - An agent sees only their own customers (Priority: P1)

Sonia (role Agent) signs in to the admin panel and opens Users. She sees only her own customers.

- **Her customers are:**
  - customers whose lead Sonia owns in the CRM (the same rule the CRM uses for her), and
  - customers she created herself in the admin panel.
- **Everything else is hidden:** other agents' customers, customers with no agent, and staff accounts.

**Why this priority**: This is the whole request. Today every agent sees every customer.

**Independent Test**:
1. Create two agents. Give each one a CRM customer, plus one customer with no agent.
2. Sign in as each agent. Each one sees exactly their own customer.
3. Opening another customer's page by id returns 404.

**Acceptance Scenarios**:

1. **Given** a customer whose CRM lead is Sonia's, **When** Sonia opens Users, **Then** the customer is listed.
2. **Given** a customer whose CRM lead is Ali's, **When** Sonia opens Users, **Then** the customer is not listed, and `/admin/users/{id}` (show, edit, update, delete, password, status, CRM files, PDF) answers 404.
3. **Given** Sonia adds a customer in the panel, **When** she opens Users, **Then** that customer is hers.
4. **Given** the dashboard, **When** Sonia opens it, **Then** the Users number and the latest-users list count only her customers.
5. **Given** "Generate passwords", **When** Sonia presses it, **Then** it only touches her own customers.

---

### User Story 2 - Manager, CSD and Admin see everyone (Priority: P1)

**Acceptance Scenarios**:

1. **Given** a role with the new permission **Customers → "See all customers"**, **When** its user opens Users, **Then** every user is listed (as today), with an **Agent** column.
2. **Given** the system administrator, **Then** they always see everything.
3. **Given** Sir moves a person to a role that has the tick, **Then** that person sees everyone with no other change.

---

### Edge Cases

- **A customer with two agents in the CRM** (two leads with the same email) is visible to both agents.
- **A lead transferred in the CRM** moves the customer on the website at the next sync (every 5 min).
- **A customer who signed up on the website with no agent** is visible only to see-all roles.
- **An agent's website email must be their CRM email.** The CRM Global button already needs this. If they differ, the agent sees only the customers they created.
- **The CRM not updated yet** (no `agentEmails` in its answer): ownership is left untouched, never wiped.
- **An agent cannot give anyone a staff role.** Their form offers only Customer, so they cannot promote someone to see everything.
- **The status switch can change only `status`.** Before this change it could write any column, e.g. `crm_email`, which would have opened another customer's CRM record.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: New permission `users.view_all` ("See all customers") in Roles & Permissions.
- **FR-002**: The migration gives it to the roles whose slug is `super-admin`, `admin`, `administrator`, `manager` or `csd`. Sir can change this in Roles & Permissions.
- **FR-003**: Without `users.view_all`, an admin sees only customers (role `customer`) that have an ownership row with their email. This applies to the list, dashboard, show, edit, update, delete, change password, status, CRM link, generate passwords, and CRM file/PDF.
- **FR-004**: The CRM sync stores each customer's agent emails (from the CRM's `agentEmails`) as ownership rows with source `crm`, replacing the old `crm` rows.
- **FR-005**: A customer created in the panel gets an ownership row for its creator, with source `panel`.
- **FR-006**: The CRM `/api/external/customers` answer adds `agentEmails` (lower-case emails of the lead owners) and `agentName`. The change is additive and every old field is unchanged.

### Key Entities

- **customer_agents**: `user_id` → users, `agent_email` (lower-case), `source` (`crm` | `panel`). One row per customer per agent.

## Success Criteria *(mandatory)*

- **SC-001**: With two agents and a manager, each agent's Users list contains exactly their customers, and the manager's list contains all of them.
- **SC-002**: Every per-user route answers 404 for a customer outside the agent's list.
- **SC-003**: Old columns in the CRM answer are byte-identical before and after (tested on Postgres).
