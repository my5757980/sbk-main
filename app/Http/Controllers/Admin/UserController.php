<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\AdminRole;
use App\Models\CrmLead;
use App\Models\CustomerAgent;
use App\Models\User;
use App\Services\Crm\CrmClient;
use App\Services\Crm\CrmCustomerSync;
use App\Services\Crm\CrmPasswords;
use App\Services\Crm\CrmRecord;
use App\Services\Crm\CrmSigner;
use App\Services\Crm\CrmStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, CrmCustomerSync $crmSync)
    {
        try {
            // Keeps CRM customers current even where the 5-minute cron is not set up.
            if (CrmSigner::configured() && CrmStatus::migrated()) {
                $crmSync->runIfStale(5);
            }
            $crmLastSync = CrmCustomerSync::lastRun();
            $crmProblem = CrmStatus::problem();
            $admin = $this->admin();
            $crmAwaitingPasswords = Schema::hasColumn('users', 'crm_password_generated_at')
                ? CrmPasswords::awaitingPassword()->visibleTo($admin)->count()
                : 0;

            // An agent gets only their own customers; "See all customers" gets everyone plus an Agent column.
            $seesAll = $admin->canSeeAllCustomers();
            $data = User::visibleTo($admin)
                ->when($seesAll && CustomerAgent::ready(), fn ($query) => $query->with('agents'))
                ->orderBy('created_at', 'DESC')
                ->get();
            $agentNames = $seesAll ? $this->agentNames() : [];
            $roleOptions = $this->roleOptions();
            // CRM customers with no email in the CRM (no website account possible yet).
            $crmLeads = CrmLead::ready()
                ? CrmLead::visibleTo($admin)->orderByDesc('last_invoice_at')->get()
                : collect();
            return view('admin.users.index', compact('data', 'roleOptions', 'crmLastSync', 'crmProblem', 'crmAwaitingPasswords', 'seesAll', 'agentNames', 'crmLeads'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function create()
    {
        $data = new User(['status' => 1, 'role' => 'customer']);
        $roleOptions = $this->roleOptions();
        $form = [
            'heading' => 'Create User',
            'method' => 'POST',
            'action' => route('admin.users.store'),
            'cancel_url' => route('admin.users.index'),
        ];

        return view('admin.users.form', compact('data', 'roleOptions', 'form'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        [$firstName, $lastName] = $this->splitName($validated['name']);

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country' => $validated['country'],
            'role' => $validated['role'],
            'status' => $request->boolean('status'),
            'password' => Hash::make($validated['password']),
        ]);

        $this->syncAdminPanelAccount($user, $validated['password']);

        // A customer added here belongs to whoever added them.
        if ($user->role === 'customer') {
            CustomerAgent::assign($user, $this->admin()->email, CustomerAgent::SOURCE_PANEL);
        }

        return redirect()->route('admin.users.index')->with('success', 'User has been created successfully.');
    }

    public function show(Request $request, $id, CrmClient $crmClient)
    {
        $data = $this->visibleUser($id);

        // The customer's complete CRM record (invoices, payments, cars, photos, documents).
        $crm = null;
        if ($data->hasCrmRecord()) {
            $record = $crmClient->customerRecord($data->crm_email, $request->boolean('refresh'));
            $crm = [
                'record' => $record ? CrmRecord::forAudience($record, false) : null,
                'unavailable' => $record === null,
                'fileUrl' => fn ($file, $download = false) => route('admin.users.show.crm-file', ['user' => $data->id, 'file' => $file['id']] + ($download ? ['download' => 1] : [])),
                'pdfUrl' => fn ($invoice, $download = false) => route('admin.users.show.crm-pdf', ['user' => $data->id, 'invoice' => $invoice['id']] + ($download ? ['download' => 1] : [])),
                'audience' => 'admin',
                'tracking' => \App\Services\VehicleTracking::forUser($data, $record),
                'trackingUrl' => $this->admin()->hasAdminPermission('users.edit')
                    ? route('admin.users.edit.vehicle-tracking', $data->id) : null,
            ];
        }

        return view('admin.users.show', compact('data', 'crm'));
    }

    public function edit($id)
    {
        $data = $this->visibleUser($id);
        $roleOptions = $this->roleOptions();
        $form = [
            'heading' => 'Edit User',
            'method' => 'PUT',
            'action' => route('admin.users.update', $id),
            'cancel_url' => route('admin.users.index'),
        ];

        return view('admin.users.form', compact('data', 'roleOptions', 'form'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->visibleUser($id);
        $oldEmail = $user->email;
        $validated = $this->validatedData($request, $user->id);
        [$firstName, $lastName] = $this->splitName($validated['name']);

        $payload = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country' => $validated['country'],
            'role' => $validated['role'],
            'status' => $request->boolean('status'),
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);
        $this->syncAdminPanelAccount($user->fresh(), $validated['password'] ?? null, $oldEmail);

        // A password typed here is the one staff handed out, so "Generate passwords" must leave it alone.
        if (!empty($validated['password']) && Schema::hasColumn('users', 'crm_password_generated_at')) {
            $user->forceFill(['crm_password_generated_at' => now()])->save();
        }
        if (!empty($validated['password'])) {
            $this->linkPendingCrm($user);
        }

        return redirect()->route('admin.users.index')->with('success', 'User has been updated successfully.');
    }

    /**
     * The Password field on the user page: a new password typed twice. Only
     * its hash is stored, so the current password is never shown.
     */
    public function changePassword(Request $request, $id)
    {
        $user = $this->visibleUser($id);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:100', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);
        $this->syncAdminPanelAccount($user->fresh(), $validated['password']);

        // A password set by staff must not be replaced by "Generate passwords".
        if (Schema::hasColumn('users', 'crm_password_generated_at')) {
            $user->forceFill(['crm_password_generated_at' => now()])->save();
        }
        $this->linkPendingCrm($user);

        return redirect()->route('admin.users.show', $user->id)->with('success', 'Password has been changed.');
    }

    public function destroy($id)
    {
        $data = $this->visibleUser($id);

        try {
            if ($data->logo != '' && file_exists(uploadsDir() . $data->logo)) {
                unlink(uploadsDir() . $data->logo);
            }
            // A deleted member of staff must not keep their admin-panel login.
            if (!in_array($data->role, [null, '', 'customer'], true)) {
                Admin::where('email', $data->email)->where('is_system_admin', 0)
                    ->update(['is_active' => 0, 'admin_role_id' => null]);
            }
            $data->delete();
            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User has been deleted successfully.');
        } catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
    public function updateStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            // Find the user by ID, among the ones this admin may see
            $user = User::visibleTo($this->admin())->find($request->id);

            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ]);
            }

            // The switch in the list is the only caller: nothing but status may be written here
            // (any other column - role, crm_email - would bypass the rules around it).
            if ($request->input('column', 'status') !== 'status') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Only the status can be changed here.',
                ]);
            }
            $user->status = $request->status ? 1 : 0;
            $user->save(); // Use save instead of update for better clarity

            // Staff: the switch also opens / closes their admin-panel login.
            if (!in_array($user->role, [null, '', 'customer'], true)) {
                $this->syncAdminPanelAccount($user);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Status has been updated',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function validatedData(Request $request, $userId = null)
    {
        $passwordRules = $userId
            ? ['nullable']
            : ['required'];
        $roles = implode(',', array_keys($this->roleOptions()));

        // A staff role's admin-panel login is this email. The main administrator's
        // own email cannot be given to a second person: their login would stay the
        // administrator's, so the password typed here would never work.
        $notSystemAdmin = function ($attribute, $value, $fail) use ($request, $userId) {
            $current = $userId ? (string) optional(User::find($userId))->email : '';
            if ($request->input('role', 'customer') === 'customer' || strcasecmp($current, (string) $value) === 0) {
                return;
            }
            if (Admin::where('email', $value)->where('is_system_admin', 1)->exists()) {
                $fail('This email is already the login of the main administrator. Use a different email for this user.');
            }
        };

        return $request->validate([
            'name' => ['required', 'string', 'max:192'],
            'email' => ['required', 'email', 'max:192', 'unique:users,email,' . $userId, $notSystemAdmin],
            'phone' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:120'],
            'role' => ['required', 'in:' . $roles],
            'password' => $passwordRules,
        ]);
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [$parts[0] ?? $name, $parts[1] ?? ''];
    }

    private function roleOptions(): array
    {
        // Only an admin who sees every customer may hand out staff roles; an agent
        // could otherwise give someone (or a second account) "See all customers".
        if (!$this->admin()->canSeeAllCustomers()) {
            return ['customer' => 'Customer'];
        }

        return ['customer' => 'Customer'] + AdminRole::orderBy('name')->pluck('name', 'slug')->toArray();
    }

    private function admin(): Admin
    {
        return auth('admin')->user();
    }

    /** A user this admin may see (see User::scopeVisibleTo), or 404. */
    private function visibleUser($id): User
    {
        return User::visibleTo($this->admin())->findOrFail($id);
    }

    /** Staff names by lower-case email, for the Agent column. */
    private function agentNames(): array
    {
        return Admin::get(['first_name', 'last_name', 'email'])
            ->mapWithKeys(fn ($admin) => [CustomerAgent::normalize($admin->email) => trim($admin->fullName()) ?: $admin->email])
            ->all();
    }

    /**
     * Staff setting a customer's password vouch for the account, as "Link now"
     * does: a CRM customer waiting on this very email is linked, so the customer
     * sees their invoices as soon as they sign in with it.
     */
    private function linkPendingCrm(User $user): void
    {
        $pending = (string) $user->crm_pending_email;
        if (!$user->crm_email && $pending !== '' && strtolower((string) $user->email) === $pending) {
            $user->forceFill(['crm_email' => $pending, 'crm_pending_email' => null])->save();
        }
    }

    private function syncAdminPanelAccount(User $user, ?string $plainPassword = null, ?string $oldEmail = null): void
    {
        $adminRole = Admin::roleFor($user);
        $oldEmail = $oldEmail ?: $user->email;

        if (!$adminRole) {
            Admin::where('email', $oldEmail)
                ->where('is_system_admin', 0)
                ->update([
                    'is_active' => 0,
                    'admin_role_id' => null,
                ]);

            return;
        }

        $admin = Admin::where('email', $oldEmail)
            ->orWhere('email', $user->email)
            ->first();

        if ($admin && (int) $admin->is_system_admin === 1) {
            return;
        }

        $payload = Admin::profileFrom($user) + [
            'is_active' => (int) $user->status === 1,
            'admin_role_id' => $adminRole->id,
        ];

        if ($plainPassword !== null && $plainPassword !== '') {
            $payload['password'] = Hash::make($plainPassword);
        } elseif (!$admin) {
            $payload['password'] = $user->password;
        }

        if ($admin) {
            $admin->update($payload);
            return;
        }

        Admin::create($payload);
    }
}
