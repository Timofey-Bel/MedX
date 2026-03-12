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
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id')->comment('Автоинкрементный ID записи');
            $table->string('product_id', 50)->index('idx_product_id')->comment('ID товара (совпадает с products.id)');
            $table->string('price_type_id', 50)->index('idx_price_type_id')->comment('ID типа цены (из price_types)');
            $table->decimal('price', 10)->comment('Цена за единицу');
            $table->string('currency', 10)->nullable()->comment('Валюта');
            $table->string('representation')->nullable()->comment('Представление цены (например, "125 руб за шт")');

            $table->unique(['product_id', 'price_type_id'], 'uk_product_price_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
