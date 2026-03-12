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
        Schema::create('pb_pages_history', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('page_id')->index('page_id');
            $table->integer('user_id')->nullable();
            $table->string('action', 50)->nullable();
            $table->longText('snapshot')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pb_pages_history');
    }
};
