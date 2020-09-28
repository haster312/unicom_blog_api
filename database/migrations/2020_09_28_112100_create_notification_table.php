<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('article_id')->nullable();
            $table->tinyInteger('type');
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->tinyInteger('seen')->default(0);
            $table->integer('created_at');
            $table->integer('updated_at');
        });

        Schema::table('user_profile', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('target_id')->references('id')->on('users');
            $table->foreign('article_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification');
    }
}
