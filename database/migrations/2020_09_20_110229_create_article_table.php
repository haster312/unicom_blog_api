<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('slug', 100);
            $table->text('short_content')->nullable();
            $table->text('content');
            $table->unsignedBigInteger('cover_id')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->integer('view_count')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->integer('created_at');
            $table->integer('updated_at');
        });

        Schema::table('article', function (Blueprint $table) {
            $table->foreign('author_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('category');
            $table->foreign('thumbnail_id')->references('id')->on('image');
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE article ADD FULLTEXT fulltext_index (short_content, content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article');
    }
}
