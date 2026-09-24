<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->integer('first_user_id')->nullable();
            $table->integer('second_user_id')->nullable();
            $table->tinyInteger('is_deleted_by_first_user')->default(0);
            $table->tinyInteger('is_deleted_by_second_user')->default(0);
            $table->tinyInteger('is_deleted_by')->nullable();
            $table->string('last_message_type',40)->nullable();
            $table->integer('last_message_sender_id')->nullable();
            $table->string('last_message',200)->nullable();
            $table->dateTime('last_message_date_time')->nullable();
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
        Schema::dropIfExists('chats');
    }
}
