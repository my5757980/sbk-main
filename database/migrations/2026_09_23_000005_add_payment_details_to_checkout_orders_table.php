<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkout_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('checkout_orders', 'payment_percentage')) {
                $table->unsignedTinyInteger('payment_percentage')->default(100)->after('payment_method');
            }

            if (!Schema::hasColumn('checkout_orders', 'remaining_amount')) {
                $table->decimal('remaining_amount', 12, 2)->default(0)->after('paid_amount');
            }

            if (!Schema::hasColumn('checkout_orders', 'paypal_order_id')) {
                $table->string('paypal_order_id')->nullable()->after('remaining_amount');
            }

            if (!Schema::hasColumn('checkout_orders', 'payment_status')) {
                $table->string('payment_status', 60)->default('pending')->after('paypal_order_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('checkout_orders', function (Blueprint $table) {
            foreach (['payment_status', 'paypal_order_id', 'remaining_amount', 'payment_percentage'] as $column) {
                if (Schema::hasColumn('checkout_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
