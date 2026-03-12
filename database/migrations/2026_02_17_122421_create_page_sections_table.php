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
        Schema::create('page_sections', function (Blueprint $table) {
            $table->comment('Секции страниц для визуального конструктора');
            $table->integer('id', true);
            $table->string('guid', 8)->nullable()->unique('guid');
            $table->string('name')->comment('Название секции');
            $table->string('slug')->unique('slug')->comment('URL-friendly название');
            $table->longText('html')->comment('HTML код секции');
            $table->longText('css')->nullable()->comment('CSS стили секции');
            $table->longText('js')->nullable()->comment('JavaScript код секции');
            $table->string('thumbnail')->nullable()->comment('Превью секции');
            $table->string('category', 100)->nullable()->default('general')->index('category')->comment('Категория секции');
            $table->integer('sort_order')->nullable()->default(0)->index('sort_order')->comment('Порядок сортировки');
            $table->boolean('active')->nullable()->default(true)->index('active')->comment('Активна ли секция');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->integer('created_by')->nullable()->comment('ID пользователя-создателя');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
