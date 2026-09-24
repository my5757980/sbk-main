<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('chat_id')->nullable();
            $table->integer('sender_id')->nullable();
            $table->integer('receiver_id')->nullable();
            $table->string('type',40);
            $table->tinyInteger('is_deleted_by_first_user')->default(0);
            $table->tinyInteger('is_deleted_by_second_user')->default(0);
            $table->tinyInteger('is_read_by_first_user')->default(0);
            $table->tinyInteger('is_read_by_second_user')->default(0);
            $table->text('message')->nullable();
            $table->string('attachment_type', 50)->nullable();
            $table->string('attachment')->nullable();
            $table->tinyInteger('is_read')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
