<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_comment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->unsignedBigInteger('parent');
            $table->integer('created_at');
            $table->integer('updated_at');
        });

        Schema::table('article_comment', function (Blueprint $table) {
            $table->foreign('article_id')->references('id')->on('article');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_comment');
    }
}
