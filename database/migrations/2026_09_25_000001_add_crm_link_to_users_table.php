<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ties a website account to a CRM customer (see app/Services/Crm).
     * crm_email: the CRM customer this account shows; crm_source: the account
     * was created by the CRM sync; crm_pending_email: an existing account with
     * a CRM customer's email that links once the owner proves the email.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'crm_email')) {
                $table->string('crm_email')->nullable()->index()->after('role');
            }
            if (!Schema::hasColumn('users', 'crm_source')) {
                $table->boolean('crm_source')->default(false)->after('crm_email');
            }
            if (!Schema::hasColumn('users', 'crm_pending_email')) {
                $table->string('crm_pending_email')->nullable()->after('crm_source');
            }
            if (!Schema::hasColumn('users', 'crm_summary')) {
                $table->json('crm_summary')->nullable()->after('crm_pending_email');
            }
            if (!Schema::hasColumn('users', 'crm_synced_at')) {
                $table->timestamp('crm_synced_at')->nullable()->after('crm_summary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'crm_email')) {
                $table->dropIndex(['crm_email']);
            }
            foreach (['crm_synced_at', 'crm_summary', 'crm_pending_email', 'crm_source', 'crm_email'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
