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
        Schema::disableForeignKeyConstraints();
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('chat_name', 250);
            $table->boolean('isGroupChat')->default(false);

            // latest message
            $table->unsignedBigInteger('message_id')->nullable();

            //people in chat
            $table->unsignedBigInteger('user_id')->nullable();

            $table
                ->foreign('message_id')
                ->references('id')
                ->on('messages')
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
        Schema::dropIfExists('chats');
    }
}
