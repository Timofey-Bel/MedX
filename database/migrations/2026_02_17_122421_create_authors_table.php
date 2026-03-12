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
        Schema::create('authors', function (Blueprint $table) {
            $table->increments('id')->comment('Автоинкрементный ID записи');
            $table->string('product_id', 50)->index('idx_product_id')->comment('ID товара (совпадает с products.id)');
            $table->string('author_name')->index('idx_author_name')->comment('Имя автора');

            $table->unique(['product_id', 'author_name'], 'uk_product_author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
