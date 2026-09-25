<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\AdminRole;
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
            $crmAwaitingPasswords = Schema::hasColumn('users', 'crm_password_generated_at')
                ? CrmPasswords::awaitingPassword()->count()
                : 0;

            $data = User::orderBy('created_at', 'DESC')->get();
            $roleOptions = $this->roleOptions();
            return view('admin.users.index', compact('data', 'roleOptions', 'crmLastSync', 'crmProblem', 'crmAwaitingPasswords'));
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

        return redirect()->route('admin.users.index')->with('success', 'User has been created successfully.');
    }

    public function show(Request $request, $id, CrmClient $crmClient)
    {
        $data = User::findOrFail($id);

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
            ];
        }

        return view('admin.users.show', compact('data', 'crm'));
    }

    public function edit($id)
    {
        $data = User::findOrFail($id);
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
        $user = User::findOrFail($id);
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

        return redirect()->route('admin.users.index')->with('success', 'User has been updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $data = User::findOrFail($id);
            if ($data->logo != '' && file_exists(uploadsDir() . $data->logo)) {
                unlink(uploadsDir() . $data->logo);
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
            // Find the user by ID
            $user = User::find($request->id);

            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ]);
            }

            // Update the user's status based on the request
            $user->{$request->column} = $request->status;
            $user->save(); // Use save instead of update for better clarity

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

        return $request->validate([
            'name' => ['required', 'string', 'max:192'],
            'email' => ['required', 'email', 'max:192', 'unique:users,email,' . $userId],
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
        return ['customer' => 'Customer'] + AdminRole::orderBy('name')->pluck('name', 'slug')->toArray();
    }

    private function syncAdminPanelAccount(User $user, ?string $plainPassword = null, ?string $oldEmail = null): void
    {
        $adminRole = AdminRole::where('slug', $user->role)->first();
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

        $payload = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'email' => $user->email,
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
