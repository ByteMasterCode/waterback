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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->boolean('isSale')->default(false);
            $table->json('topicons')->nullable();
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('language_id')->constrained();
            $table->boolean('isCashback')->default(false);
            $table->json('cover')->nullable();
            $table->text('description')->nullable();
            $table->text('brief_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
