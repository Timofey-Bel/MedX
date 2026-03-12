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
        Schema::create('_imported_items', function (Blueprint $table) {
            $table->bigInteger('id')->nullable();
            $table->string('name')->nullable();
            $table->string('offer_id', 50)->nullable();
            $table->string('primary_image')->nullable();
            $table->decimal('price', 19)->nullable();
            $table->decimal('volume_weight')->nullable();
            $table->json('images')->nullable();
            $table->string('sku', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_imported_items');
    }
};
