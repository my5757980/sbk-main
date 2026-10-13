<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The phones that get the SBK app's notifications (App\Services\App\AppPush):
 * one row per phone, belonging to a chat customer (sbk_chat_guests.id).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sbk_app_push', function (Blueprint $table) {
            $table->id();
            $table->string('who', 10)->default('guest');
            $table->unsignedBigInteger('who_id')->index();
            $table->string('token', 255)->unique();
            $table->string('platform', 20)->default('android');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sbk_app_push');
    }
};
