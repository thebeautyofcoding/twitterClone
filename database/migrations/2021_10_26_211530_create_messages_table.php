<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('content');
            $table->unsignedBigInteger('chat_id')->nullable();

            // read_by
            $table->unsignedBigInteger('user_id')->nullable();

            $table
                ->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table
                ->foreign('chat_id')
                ->references('id')
                ->on('chats')
                ->onDelete('cascade');
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('messages');
    }
}
