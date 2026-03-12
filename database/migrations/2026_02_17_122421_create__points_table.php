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
        Schema::create('_points', function (Blueprint $table) {
            $table->string('id', 50)->nullable();
            $table->decimal('la', 9, 6)->nullable()->comment('широта');
            $table->decimal('lo', 9, 6)->nullable()->comment('долгота');
            $table->json('json')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_points');
    }
};
