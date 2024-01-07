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
        Schema::create('friends_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user_one')->nullable(false);
            $table->unsignedBigInteger('id_user_two')->nullable(false);
            $table->enum('status', ['expectation', 'confirmation', 'deviation']);
            $table->timestamps();
            $table->foreign('id_user_one')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_user_two')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friends_requests');
    }
};
