<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('crm_email', 192);
            $table->string('invoice_id', 64);
            $table->string('vehicle_key', 64);
            $table->string('stage', 32);
            $table->date('occurred_on');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('admin_name', 192);
            $table->timestamps();
            $table->index(['user_id', 'invoice_id', 'vehicle_key'], 'vehicle_tracking_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_tracking_events');
    }
};
