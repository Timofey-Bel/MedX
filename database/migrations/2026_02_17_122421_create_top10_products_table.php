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
        Schema::create('top10_products', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('product_id', 50)->unique('unique_product');
            $table->integer('sort')->default(0)->index('idx_sort');
            $table->boolean('active')->default(true)->index('idx_active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top10_products');
    }
};
