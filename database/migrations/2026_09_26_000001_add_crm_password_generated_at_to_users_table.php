<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When an admin last generated this customer's login password (see
     * App\Services\Crm\CrmPasswords). The "all CRM customers" button only
     * covers accounts that never had one, so a second click cannot break
     * passwords already handed out.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'crm_password_generated_at')) {
                $table->timestamp('crm_password_generated_at')->nullable()->after('crm_synced_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'crm_password_generated_at')) {
                $table->dropColumn('crm_password_generated_at');
            }
        });
    }
};
