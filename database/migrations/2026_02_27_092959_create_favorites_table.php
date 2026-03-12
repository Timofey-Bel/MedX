<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Создание таблицы favorites для хранения избранных товаров пользователей
     * Миграция из legacy системы: избранное переносится из сессии в БД
     */
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID пользователя');
            $table->string('product_id', 50)->comment('ID товара (offer_id)');
            $table->timestamp('created_at')->useCurrent()->comment('Дата добавления в избранное');
            
            // Индексы для быстрого поиска
            $table->index('user_id');
            $table->index('product_id');
            $table->unique(['user_id', 'product_id'], 'user_product_unique');
            
            // Внешний ключ на таблицу users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
