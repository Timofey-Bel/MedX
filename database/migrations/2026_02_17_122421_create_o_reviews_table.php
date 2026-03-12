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
        Schema::create('o_reviews', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('review_id')->unique('uk_review_id')->comment('ID отзыва из Ozon API (UUID)');
            $table->bigInteger('sku')->nullable()->index('idx_sku')->comment('SKU товара для связи с o_products');
            $table->integer('rating')->nullable()->index('idx_rating')->comment('Рейтинг от 1 до 5');
            $table->text('text')->nullable()->comment('Текст отзыва');
            $table->dateTime('date')->nullable()->index('idx_date')->comment('Дата отзыва');
            $table->string('state', 50)->nullable()->comment('Состояние отзыва (например, PROCESSED, UNPROCESSED)');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_reviews');
    }
};
