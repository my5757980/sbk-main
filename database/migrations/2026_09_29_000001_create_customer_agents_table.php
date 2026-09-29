<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Whose customer is whom (see User::scopeVisibleTo): one row per customer
     * per agent, by the agent's email - the CRM lead's owner (source "crm") or
     * whoever added the customer in the admin panel (source "panel").
     *
     * Also adds the "See all customers" permission (users.view_all), ticked for
     * the roles that must keep seeing everyone: Super Admin, Admin, Manager, CSD.
     */
    public function up(): void
    {
        if (!Schema::hasTable('customer_agents')) {
            Schema::create('customer_agents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('agent_email', 191)->index();
                $table->string('source', 10);
                $table->timestamps();
                $table->unique(['user_id', 'agent_email']);
            });
        }

        if (!Schema::hasTable('admin_permissions')) {
            return;
        }

        $permissionId = DB::table('admin_permissions')->where('name', 'users.view_all')->value('id');
        if (!$permissionId) {
            $permissionId = DB::table('admin_permissions')->insertGetId([
                'module' => 'users',
                'action' => 'view_all',
                'name' => 'users.view_all',
                'label' => 'See all customers',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $roleIds = DB::table('admin_roles')
            ->whereIn('slug', ['super-admin', 'admin', 'administrator', 'manager', 'csd'])
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table('admin_permission_admin_role')->insertOrIgnore([
                'admin_role_id' => $roleId,
                'admin_permission_id' => $permissionId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_agents');

        if (Schema::hasTable('admin_permissions')) {
            $permissionId = DB::table('admin_permissions')->where('name', 'users.view_all')->value('id');
            if ($permissionId) {
                DB::table('admin_permission_admin_role')->where('admin_permission_id', $permissionId)->delete();
                DB::table('admin_permissions')->where('id', $permissionId)->delete();
            }
        }
    }
};
