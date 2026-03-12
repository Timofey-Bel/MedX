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
        Schema::create('pb_page_content', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('page_id')->index('page_id');
            $table->integer('block_id')->index('block_id');
            $table->integer('sort_order')->default(0)->index('sort_order');
            $table->text('settings')->nullable();
            $table->text('html_content')->nullable();
            $table->text('css_content')->nullable();
            $table->boolean('is_visible')->nullable()->default(true);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pb_page_content');
    }
};
