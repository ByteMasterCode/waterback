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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('cover')->nullable(); // Путь к изображению
            $table->text('description')->nullable();
            $table->foreignId('language_id')->constrained(); // Внешний ключ к языку
            $table->text('brief_description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained(); // Внешний ключ к категории (может быть NULL)
            $table->foreignId('news_id')->nullable()->constrained(); // Внешний ключ к новости (может быть NULL)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
