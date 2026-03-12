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
        Schema::create('popular_categories', function (Blueprint $table) {
            $table->comment('Популярные категории для главной страницы');
            $table->integer('id', true);
            $table->string('category_id', 50)->index('idx_category_id')->comment('ID категории из таблицы tree');
            $table->integer('sort')->default(0)->index('idx_sort')->comment('Порядок сортировки');
            $table->string('image')->nullable()->comment('Путь к изображению');
            $table->boolean('active')->default(true)->index('idx_active')->comment('Активность');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popular_categories');
    }
};
