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
        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id')->comment('Автоинкрементный ID записи');
            $table->string('product_id', 50)->index('idx_product_id')->comment('ID товара (совпадает с products.id)');
            $table->string('name')->index('idx_name')->comment('Название атрибута (Наименование)');
            $table->text('value')->nullable()->comment('Значение атрибута');

            $table->unique(['product_id', 'name'], 'uk_product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
