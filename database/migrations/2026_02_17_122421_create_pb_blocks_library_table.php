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
        Schema::create('pb_blocks_library', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code', 100)->unique('code');
            $table->string('name');
            $table->string('category', 100)->nullable()->default('other')->index('category');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('html_template');
            $table->text('css_template')->nullable();
            $table->text('js_template')->nullable();
            $table->text('default_settings')->nullable();
            $table->boolean('is_system')->nullable()->default(false);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pb_blocks_library');
    }
};
