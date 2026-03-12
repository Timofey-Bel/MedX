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
        Schema::create('o_categories', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('description_category_id')->unique('description_category_id');
            $table->string('category_name');
            $table->integer('type_id')->nullable()->index('type_id');
            $table->string('type_name')->nullable();
            $table->integer('parent_id')->nullable()->index('parent_id');
            $table->boolean('disabled')->nullable()->default(false);
            $table->integer('level')->nullable()->default(0);
            $table->string('path', 500)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_categories');
    }
};
