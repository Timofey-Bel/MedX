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
        Schema::create('o_products', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('product_id')->unique('product_id');
            $table->string('offer_id')->unique('offer_id');
            $table->string('barcode')->nullable();
            $table->bigInteger('sku')->nullable()->index('idx_sku')->comment('SKU товара из Ozon API');
            $table->string('name', 500)->nullable();
            $table->decimal('height', 10)->nullable();
            $table->decimal('depth', 10)->nullable();
            $table->decimal('width', 10)->nullable();
            $table->string('dimension_unit', 10)->nullable();
            $table->decimal('weight', 10)->nullable();
            $table->string('weight_unit', 10)->nullable();
            $table->bigInteger('description_category_id')->nullable()->index('description_category_id');
            $table->bigInteger('type_id')->nullable()->index('type_id');
            $table->string('primary_image', 500)->nullable();
            $table->boolean('has_fbo_stocks')->nullable()->default(false);
            $table->boolean('has_fbs_stocks')->nullable()->default(false);
            $table->boolean('archived')->nullable()->default(false)->index('archived');
            $table->boolean('is_discounted')->nullable()->default(false);
            $table->text('quants')->nullable();
            $table->text('product_data')->nullable()->comment('Полные данные товара в JSON формате');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_products');
    }
};
