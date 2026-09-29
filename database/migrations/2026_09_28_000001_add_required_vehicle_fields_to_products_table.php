<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('ref_no')->nullable()->unique()->after('slug');
            $table->string('dimension')->nullable()->after('color');
            $table->string('m3')->nullable()->after('dimension');
            $table->string('driving_type')->nullable()->after('steering');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['ref_no']);
            $table->dropColumn(['ref_no', 'dimension', 'm3', 'driving_type']);
        });
    }
};
