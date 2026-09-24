<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->string('session_id')->nullable()->index();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('port')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('message')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->string('status', 60)->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkout_orders');
    }
};
