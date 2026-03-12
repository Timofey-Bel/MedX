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
            $table->string('id', 50)->primary();
            $table->string('category_id', 50)->nullable()->index('idx_products_category_id');
            $table->string('sku', 100)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('picture')->nullable();
            $table->string('base_unit', 20)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('weight', 10)->nullable();
            $table->boolean('is_new')->nullable();
            $table->json('attributes_json')->nullable();

            $table->unique(['id'], 'uk_products_id');
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
