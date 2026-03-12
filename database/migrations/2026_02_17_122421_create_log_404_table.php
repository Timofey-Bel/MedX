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
        Schema::create('log_404', function (Blueprint $table) {
            $table->integer('id', true);
            $table->dateTime('dt')->nullable()->useCurrent();
            $table->text('text')->nullable();
            $table->string('ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_404');
    }
};
