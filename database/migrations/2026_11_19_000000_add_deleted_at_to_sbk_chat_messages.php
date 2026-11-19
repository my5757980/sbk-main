<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Message delete ("delete for everyone"): soft-delete support on the live-chat
 * messages so a person can delete their own message. The row is kept (its text
 * and media are wiped by the app); only this timestamp is added.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sbk_chat_messages') && !Schema::hasColumn('sbk_chat_messages', 'deleted_at')) {
            Schema::table('sbk_chat_messages', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable()->after('read_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sbk_chat_messages', 'deleted_at')) {
            Schema::table('sbk_chat_messages', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
};
