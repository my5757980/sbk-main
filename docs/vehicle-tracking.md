# Manual vehicle progress

The website stores progress independently of the CRM in `vehicle_tracking_events`.
CRM invoices, payments, files, synchronization and existing account permissions are unchanged.

## Usage

Open Admin → Users → User Detail, expand an invoice, and select **Update Progress**
under the relevant car. Select a stage, optionally change the date or enter a note,
and save. A blank date uses today. Future dates are not accepted because these are
completed/current stages, not estimates. Notes and history are visible to the customer.

Stages: Not started → Vehicle Reserved → Invoice Generated → First Payment Received →
Car Booked → Inspection/Yard → In Yard → Final Payment → Shipped → Delivered to port.

All stages are manual. No stage or payment is inferred from CRM values. Earlier
steps are colored complete relative to the selected stage; dates in the history
are the actual dates entered by staff. Corrections and resets append history;
they do not erase earlier entries. The latest saved entry determines the current
stage, even when its entered date is earlier than a previous entry.

The customer sees the same timeline in **My Invoices** after opening/refreshing
the portal. Customers cannot edit progress. Staff names and recording timestamps
are shown only in the admin history. Editing requires `users.edit` and access to
that customer under the existing agent assignment rules.

Tracking is available for cars already supplied by the CRM invoice record, not
standalone reservations before a CRM record exists. A CRM vehicle ID or chassis
identifies each car; ambiguous cars cannot be edited until identified in the CRM.
Single-vehicle records with no units/vehicles use the invoice identity. Multi-car
records use their individual IDs/chassis, never list positions. Changing a car's
identifier, the linked CRM email, or moving it to another invoice does not
automatically transfer history. This avoids attaching history to a different car.
The CRM must be available to verify ownership when saving an update.

## Deploy

Deploy the feature files, then run only its migration:

```sh
php artisan migrate --path=database/migrations/2026_10_22_000001_create_vehicle_tracking_events_table.php --force
```

Rebuild route/view caches if your deployment normally caches them. Before the
table exists, the existing pages work without displaying tracking controls.

## Revert

Back up `vehicle_tracking_events` if any progress has been entered. Restore only
the feature's changes in the admin and profile controllers, admin routes and CRM
record partial, and remove its dedicated controller, service, model, view and
tests. Preserve unrelated changes, including the previous CRM totals fix.

The new table can safely remain unused after reverting the code. To remove it,
use this migration's `down()` through Laravel's migration rollback mechanism;
check migration batches first and target this migration alone. Its `down()` drops
only `vehicle_tracking_events`, which permanently removes tracking history.
It does not alter users, invoices, or the CRM database.

## Verification

```sh
php artisan test --filter='VehicleTrackingTest|CrmRecordViewTest'
```

Tests use database transactions and mocked CRM responses. They cover admin and
customer page rendering, permissions, agent ownership, input validation, outage
handling, customer isolation, escaping, corrections, reset, and vehicle identity.
