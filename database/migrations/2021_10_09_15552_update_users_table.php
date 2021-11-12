<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('users', function ($table) {
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();

            $table->string('bio')->nullable();
            $table->string('profile_pic')->nullable();
            $table->unsignedBigInteger('retweet_id')->nullable();
            $table->unsignedBigInteger('post_id_retweets')->nullable();
            $table->unsignedBigInteger('user_id_following')->nullable();
            $table->unsignedBigInteger('user_id_followers')->nullable();
            $table->unsignedBigInteger('post_like_id')->nullable();
            $table->unsignedBigInteger('post_reply_id')->nullable();
            $table->unsignedBigInteger('following_id')->nullable();
            $table->unsignedBigInteger('follower_id')->nullable();

            $table
                ->foreign('retweet_id')
                ->references('id')
                ->on('retweets')
                ->onDelete('cascade');
            $table
                ->foreign('post_id_retweets')
                ->references('id')
                ->on('posts')
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
            // $table
            //     ->foreign('following_id')
            //     ->references('id')
            //     ->on('followings')
            //     ->onDelete('cascade');
            // $table
            //     ->foreign('follower_id')
            //     ->references('id')
            //     ->on('followers')
            //     ->onDelete('cascade');
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
        Schema::table('users', function ($table) {
            Schema::dropIfExists('posts');
        });
    }
}
