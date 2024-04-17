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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained(); // Внешний ключ к заказу
            $table->foreignId('product_id')->constrained(); // Внешний ключ к продукту
            $table->integer('count'); // Количество продуктов в заказе
            $table->string('serial')->nullable(); // Серийный номер продукта (может быть NULL)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
