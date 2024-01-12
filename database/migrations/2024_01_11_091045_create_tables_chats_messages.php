<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_chats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('id_user_one')->nullable(false);
            $table->unsignedBigInteger('id_user_two')->nullable(false);
            $table->foreign('id_user_one')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_user_two')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('users_messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('chat_id')->nullable(false);
            $table->integer('sender_id')->nullable(false);
            $table->string('message')->nullable(false);
            $table->foreign('chat_id')->references('id')->on('users_chats')->onDelete('cascade');
            $table->foreign('senderId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables_chats_messages');
    }
};
