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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->nullable()->unique();
            $table->string('fullname');
            $table->string('phoneNumber')->unique();
            $table->string('password');
            $table->string('address')->nullable();
            $table->json('location')->nullable();
            $table->string('telegram_id')->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
