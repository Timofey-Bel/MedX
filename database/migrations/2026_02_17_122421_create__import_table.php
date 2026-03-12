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
        Schema::create('_import', function (Blueprint $table) {
            $table->json('json')->nullable();
            $table->double('product_id')->nullable();
            $table->string('offer_id', 50)->nullable();
            $table->boolean('has_fbo_stocks')->nullable();
            $table->boolean('has_fbs_stocks')->nullable();
            $table->boolean('archived')->nullable();
            $table->boolean('is_discounted')->nullable();
            $table->string('quants', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_import');
    }
};
