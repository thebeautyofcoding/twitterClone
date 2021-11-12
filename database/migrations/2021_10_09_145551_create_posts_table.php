<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('content')->nullable();
            $table->unsignedBigInteger('user_id_posted_by')->nullable();
            $table->unsignedBigInteger('retweet_id')->nullable();
            $table->unsignedBigInteger('post_id_retweet_data')->nullable();

            $table->unsignedBigInteger('post_like_id')->nullable();
            $table->unsignedBigInteger('post_reply_id')->nullable();

            $table
                ->foreign('user_id_posted_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('retweet_id')
                ->references('id')
                ->on('retweets')
                ->onDelete('cascade');

            $table
                ->foreign('post_like_id')
                ->references('id')
                ->on('post_likes')
                ->onDelete('cascade');

            $table
                ->foreign('post_reply_id')
                ->references('id')
                ->on('post_replies')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('posts');
    }
}
