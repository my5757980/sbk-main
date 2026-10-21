<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\VehicleTrackingEvent;
use App\Services\Crm\CrmClient;
use App\Services\VehicleTracking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function store(Request $request, string $user, CrmClient $crm)
    {
        $admin = auth('admin')->user();
        abort_unless($admin->hasAdminPermission('users.edit'), 403);
        $customer = User::visibleTo($admin)->findOrFail($user);
        abort_unless($customer->hasCrmRecord(), 404);
        abort_unless(VehicleTracking::ready(), 503, 'Vehicle tracking is not available yet.');
        $data = $request->validate([
            'invoice_id' => ['required', 'string', 'max:64'],
            'vehicle_key' => ['required', 'string', 'size:64'],
            'stage' => ['required', Rule::in(array_keys(VehicleTrackingEvent::STAGES))],
            'occurred_on' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);
        $record = $crm->customerRecord($customer->crm_email, true);
        if ($record === null) {
            return back()->withErrors(['tracking' => 'The CRM is unavailable. Progress was not saved. Please try again.'])->withInput();
        }
        $invoice = collect($record['invoices'] ?? [])->firstWhere('id', $data['invoice_id']);
        abort_unless($invoice && collect(VehicleTracking::vehicles($invoice))->contains('key', $data['vehicle_key']), 404);

        $data['occurred_on'] = $data['occurred_on'] ?? now()->toDateString();
        VehicleTrackingEvent::create($data + [
            'user_id' => $customer->id,
            'crm_email' => strtolower(trim($customer->crm_email)),
            'admin_id' => $admin->id,
            'admin_name' => mb_substr(trim($admin->fullName()) ?: 'Admin', 0, 192),
        ]);

        return redirect()->route('admin.users.show', $customer->id)->with('success', 'Vehicle progress updated.');
    }
}
