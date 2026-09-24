<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $data = AdminRole::withCount('admins', 'permissions')->latest()->get();

        return view('admin.roles.index', compact('data'));
    }

    public function create()
    {
        $data = new AdminRole();
        $permissions = $this->groupedPermissions();
        $selectedPermissions = [];
        $form = [
            'heading' => 'Create Role',
            'action' => route('admin.admin-roles.store'),
            'method' => 'POST',
        ];

        return view('admin.roles.form', compact('data', 'permissions', 'selectedPermissions', 'form'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $role = AdminRole::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.admin-roles.index')->with('success', 'Role has been added successfully.');
    }

    public function show(AdminRole $adminRole)
    {
        $adminRole->load('permissions', 'admins');

        return view('admin.roles.show', ['data' => $adminRole]);
    }

    public function edit(AdminRole $adminRole)
    {
        $data = $adminRole;
        $permissions = $this->groupedPermissions();
        $selectedPermissions = $adminRole->permissions()->pluck('admin_permissions.id')->toArray();
        $form = [
            'heading' => 'Edit Role',
            'action' => route('admin.admin-roles.update', $adminRole->id),
            'method' => 'PUT',
        ];

        return view('admin.roles.form', compact('data', 'permissions', 'selectedPermissions', 'form'));
    }

    public function update(Request $request, AdminRole $adminRole)
    {
        if ($adminRole->is_system) {
            return back()->with('error', 'System role cannot be changed.');
        }

        $validated = $this->validated($request);
        $adminRole->update([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $adminRole->id),
            'description' => $validated['description'] ?? null,
        ]);
        $adminRole->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.admin-roles.index')->with('success', 'Role has been updated successfully.');
    }

    public function destroy(AdminRole $adminRole)
    {
        if ($adminRole->is_system || $adminRole->admins()->exists()) {
            return back()->with('error', 'This role cannot be deleted.');
        }

        $adminRole->delete();

        return redirect()->route('admin.admin-roles.index')->with('success', 'Role has been deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:admin_permissions,id'],
        ]);
    }

    private function groupedPermissions()
    {
        return AdminPermission::orderBy('module')->orderBy('action')->get()->groupBy('module');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (AdminRole::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
