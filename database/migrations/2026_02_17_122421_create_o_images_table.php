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
        Schema::create('o_images', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('product_id')->index('product_id');
            $table->string('image_url', 500);
            $table->integer('image_order')->nullable()->default(0)->index('image_order');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_images');
    }
};
