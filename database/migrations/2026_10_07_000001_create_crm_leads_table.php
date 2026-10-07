<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CRM customers with NO email (app/Models/CrmLead): kept in step with the
 * CRM's feed by CrmCustomerSync so an agent sees every customer of theirs,
 * not only the ones a website account can be made for.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('crm_leads')) {
            return;
        }

        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_id', 64)->unique();
            $table->string('name', 191)->default('');
            $table->string('contact_person', 191)->default('');
            $table->string('phone', 50)->default('');
            $table->string('country', 120)->default('');
            $table->unsignedInteger('invoice_count')->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('paid', 14, 2)->default(0);
            $table->decimal('balance', 14, 2)->default(0);
            $table->timestamp('last_invoice_at')->nullable();
            $table->string('agent_email', 191)->nullable()->index();
            $table->string('agent_name', 191)->default('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};
