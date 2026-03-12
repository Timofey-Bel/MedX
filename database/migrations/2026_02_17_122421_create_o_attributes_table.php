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
        Schema::create('o_attributes', function (Blueprint $table) {
            $table->increments('id')->comment('Автоинкрементный ID записи');
            $table->string('product_id', 50)->index('product_id')->comment('ID товара (совпадает с products.id)');
            $table->string('dictionary_value_id', 50)->index('dictionary_value_id')->comment('ID атрибута');
            $table->text('value')->nullable()->comment('Значение атрибута');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_attributes');
    }
};
