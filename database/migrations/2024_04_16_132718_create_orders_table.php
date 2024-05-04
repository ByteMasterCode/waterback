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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->foreignId('user_id')->constrained(); // Внешний ключ к пользователю
            $table->foreignId('courier_id')->nullable()->constrained('users');
            $table->string('address')->nullable();
            $table->json('location')->nullable();
            $table->dateTime('accept_date')->nullable();
            $table->dateTime('delivered_date')->nullable();
            $table->boolean('is_denied')->default(false);
            $table->string('description')->nullable();
            $table->string('status');
            $table->decimal('total', 10, 2);
            $table->decimal('cashback', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
