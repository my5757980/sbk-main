<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The live chat's tables (app/Services/Chat) - the same seven the chat on
     * sbkautotrading.com has, in THIS website's database. People are not
     * copied: customers are `users`, staff are `admins`.
     *
     * Named sbk_chat_* because the site's original template already owns
     * `chats` and `chat_messages` (2022 migrations, unused); those are left
     * exactly as they are.
     *
     * Times are written by PHP in UTC into DATETIME columns, never by the
     * database, so MySQL and the SQLite used in tests behave the same.
     *
     * Also: admins.chat_visible (the old "Show in the chat" tick box) and the
     * permissions chat.desk / chat.view_all.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sbk_chat_guests')) {
            // Everybody who writes to the desk: a website customer (user_id) or a visitor (token cookie).
            Schema::create('sbk_chat_guests', function (Blueprint $table) {
                $table->id();
                $table->string('token', 40)->unique();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('name', 120)->default('');
                $table->string('email', 160)->default('')->index();
                $table->string('phone', 40)->default('');
                $table->string('ip', 45)->default('');
                $table->string('user_agent', 255)->default('');
                $table->dateTime('last_seen')->nullable();
                $table->dateTime('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sbk_chat_threads')) {
            // One conversation per customer per member of staff (admins.id).
            Schema::create('sbk_chat_threads', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('guest_id');
                $table->unsignedBigInteger('staff_id')->index();
                $table->unsignedInteger('guest_unread')->default(0);
                $table->unsignedInteger('staff_unread')->default(0);
                $table->dateTime('last_message_at')->nullable()->index();
                $table->string('last_preview', 160)->nullable();
                $table->dateTime('created_at')->nullable();
                $table->unique(['guest_id', 'staff_id']);
            });
        }

        if (!Schema::hasTable('sbk_chat_messages')) {
            // A message in a conversation (thread_id) or, with thread_id = 0, in a staff group (group_id).
            Schema::create('sbk_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('thread_id')->default(0);
                $table->unsignedBigInteger('group_id')->nullable();
                $table->boolean('from_guest')->default(false);
                $table->unsignedBigInteger('staff_id')->nullable();
                $table->string('kind', 10)->default('text');
                $table->text('body')->nullable();
                $table->string('media_path', 120)->nullable();
                $table->string('media_mime', 60)->nullable();
                $table->unsignedInteger('media_bytes')->nullable();
                $table->unsignedInteger('media_secs')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('delivered_at')->nullable();
                $table->dateTime('read_at')->nullable();
                $table->index(['thread_id', 'id']);
                $table->index(['thread_id', 'from_guest']);
                $table->index(['group_id', 'id']);
            });
        }

        if (!Schema::hasTable('sbk_chat_presence')) {
            // Green dots: the last heartbeat of a member of staff or a customer.
            Schema::create('sbk_chat_presence', function (Blueprint $table) {
                $table->string('who', 5);
                $table->unsignedBigInteger('who_id');
                $table->dateTime('last_ping');
                $table->string('where_at', 10)->default('chat');
                $table->primary(['who', 'who_id']);
            });
        }

        if (!Schema::hasTable('sbk_chat_calls')) {
            // A call's handshake; the sound and picture go browser to browser.
            Schema::create('sbk_chat_calls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('thread_id')->index();
                $table->boolean('from_guest')->default(false);
                $table->string('kind', 5)->default('voice');
                $table->mediumText('offer');
                $table->mediumText('answer')->nullable();
                $table->string('state', 10)->default('ringing')->index();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('answered_at')->nullable();
                $table->dateTime('ended_at')->nullable();
                $table->dateTime('guest_seen')->nullable();
                $table->dateTime('staff_seen')->nullable();
            });
        }

        if (!Schema::hasTable('sbk_chat_groups')) {
            Schema::create('sbk_chat_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120);
                $table->unsignedBigInteger('created_by');
                $table->dateTime('created_at')->nullable();
                $table->dateTime('last_message_at')->nullable()->index();
                $table->string('last_preview', 160)->nullable();
            });
        }

        if (!Schema::hasTable('sbk_chat_group_members')) {
            Schema::create('sbk_chat_group_members', function (Blueprint $table) {
                $table->unsignedBigInteger('group_id');
                $table->unsignedBigInteger('staff_id')->index();
                $table->boolean('is_owner')->default(false);
                $table->unsignedInteger('unread')->default(0);
                $table->unsignedBigInteger('last_read_id')->default(0);
                $table->dateTime('joined_at')->nullable();
                $table->primary(['group_id', 'staff_id']);
            });
        }

        // "Show in the chat": null = by role, 'yes' = always, 'no' = never.
        if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'chat_visible')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->string('chat_visible', 3)->nullable();
            });
        }

        $this->addPermissions();
    }

    public function down(): void
    {
        foreach (['sbk_chat_group_members', 'sbk_chat_groups', 'sbk_chat_calls', 'sbk_chat_presence',
                  'sbk_chat_messages', 'sbk_chat_threads', 'sbk_chat_guests'] as $table) {
            Schema::dropIfExists($table);
        }

        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'chat_visible')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('chat_visible');
            });
        }

        if (Schema::hasTable('admin_permissions')) {
            $ids = DB::table('admin_permissions')->whereIn('name', ['chat.desk', 'chat.view_all'])->pluck('id');
            DB::table('admin_permission_admin_role')->whereIn('admin_permission_id', $ids)->delete();
            DB::table('admin_permissions')->whereIn('id', $ids)->delete();
        }
    }

    /** chat.desk for the roles that answer customers, chat.view_all for the admins. */
    private function addPermissions(): void
    {
        if (!Schema::hasTable('admin_permissions')) {
            return;
        }

        $grants = [
            'desk' => ['Chat desk (answer customers, shown in their list)',
                       ['super-admin', 'admin', 'administrator', 'manager', 'csd', 'agent']],
            'view_all' => ['See every conversation',
                           ['super-admin', 'admin', 'administrator']],
        ];

        foreach ($grants as $action => [$label, $slugs]) {
            $name = 'chat.' . $action;
            $id = DB::table('admin_permissions')->where('name', $name)->value('id');
            if (!$id) {
                $id = DB::table('admin_permissions')->insertGetId([
                    'module' => 'chat',
                    'action' => $action,
                    'name' => $name,
                    'label' => $label,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach (DB::table('admin_roles')->whereIn('slug', $slugs)->pluck('id') as $roleId) {
                DB::table('admin_permission_admin_role')->insertOrIgnore([
                    'admin_role_id' => $roleId,
                    'admin_permission_id' => $id,
                ]);
            }
        }
    }
};
