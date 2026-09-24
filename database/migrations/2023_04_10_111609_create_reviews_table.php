<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable();
            $table->foreignId('traveller_id')->nullable();
            $table->foreignId('owner_id')->nullable();
            $table->tinyInteger('review_from')->nullable()
                ->comment("Traveller(0) | Owner(1)");
            $table->float('star')->nullable();
            $table->tinyInteger('recommended')->default(0)->comment("(1) Yes | (0) No");
            $table->text('feedback')->nullable();
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
        Schema::dropIfExists('reviews');
    }
}
