<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('action');
            $table->string('name')->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('admin_permission_admin_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_role_id')->constrained('admin_roles')->cascadeOnDelete();
            $table->foreignId('admin_permission_id')->constrained('admin_permissions')->cascadeOnDelete();
            $table->unique(['admin_role_id', 'admin_permission_id'], 'admin_role_permission_unique');
        });

        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'admin_role_id')) {
                $table->foreignId('admin_role_id')->nullable()->after('is_system_admin')->constrained('admin_roles')->nullOnDelete();
            }
        });

        $roleId = DB::table('admin_roles')->insertGetId([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Full access to all admin panel modules.',
            'is_system' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permissions = [];
        foreach (config('admin_permissions', []) as $module => $group) {
            foreach ($group['actions'] as $action => $label) {
                $permissions[] = [
                    'module' => $module,
                    'action' => $action,
                    'name' => $module . '.' . $action,
                    'label' => $label,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('admin_permissions')->insert($permissions);
        $permissionIds = DB::table('admin_permissions')->pluck('id');
        foreach ($permissionIds as $permissionId) {
            DB::table('admin_permission_admin_role')->insert([
                'admin_role_id' => $roleId,
                'admin_permission_id' => $permissionId,
            ]);
        }

        DB::table('admins')
            ->where('is_system_admin', 1)
            ->orWhereNull('admin_role_id')
            ->update(['admin_role_id' => $roleId]);
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'admin_role_id')) {
                $table->dropConstrainedForeignId('admin_role_id');
            }
        });

        Schema::dropIfExists('admin_permission_admin_role');
        Schema::dropIfExists('admin_permissions');
        Schema::dropIfExists('admin_roles');
    }
};
