<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPagesTableAddColumnSubTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->text('page_subtitle')->nullable()->after('page_title');
            $table->string('status', 60)->default('published')->after('meta_description');
            $table->string('type', 60)->default('default')->after('status');
            $table->text('image')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('page_subtitle');
            $table->dropColumn('status');
            $table->dropColumn('type');
            $table->dropColumn('image');
        });
    }
}
