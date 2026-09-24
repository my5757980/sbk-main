<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkout_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('checkout_orders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('checkout_orders', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0)->after('subtotal');
            }

            if (!Schema::hasColumn('checkout_orders', 'order_date')) {
                $table->date('order_date')->nullable()->after('paid_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('checkout_orders', function (Blueprint $table) {
            if (Schema::hasColumn('checkout_orders', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }

            if (Schema::hasColumn('checkout_orders', 'order_date')) {
                $table->dropColumn('order_date');
            }

            if (Schema::hasColumn('checkout_orders', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });
    }
};
