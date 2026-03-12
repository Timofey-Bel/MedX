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
        Schema::create('product_hashtags', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('product_id', 50)->index('idx_product_id');
            $table->string('value')->index('idx_value');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['product_id', 'value'], 'unique_product_hashtag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_hashtags');
    }
};
