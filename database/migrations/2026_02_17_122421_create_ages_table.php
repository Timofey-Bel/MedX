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
        Schema::create('ages', function (Blueprint $table) {
            $table->increments('id')->comment('Автоинкрементный ID записи');
            $table->string('product_id', 50)->index('idx_product_id')->comment('ID товара (совпадает с products.id)');
            $table->string('age')->index('idx_age')->comment('Значение возраста из атрибутов товара');

            $table->unique(['product_id', 'age'], 'uk_product_age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ages');
    }
};
