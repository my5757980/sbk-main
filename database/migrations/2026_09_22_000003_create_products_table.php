<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_brand_id')->constrained('vehicle_brands')->restrictOnDelete();
            $table->foreignId('vehicle_model_id')->constrained('vehicle_models')->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('stock_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('mileage')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->string('body_type')->nullable();
            $table->string('engine')->nullable();
            $table->string('color')->nullable();
            $table->string('steering')->nullable();
            $table->string('location')->nullable();
            $table->string('tag')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('images')->nullable();
            $table->string('status', 60)->default('published');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
