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
        Schema::create('app_desktop_shortcuts', function (Blueprint $table) {
            $table->comment('Ярлыки установленных приложений на рабочем столе');
            $table->integer('id', true);
            $table->string('app_id', 50)->index('idx_app_id')->comment('ID приложения');
            $table->string('title', 100)->comment('Название ярлыка');
            $table->string('icon', 100)->comment('Иконка (material icon или путь)');
            $table->string('icon_color', 20)->nullable()->default('#666')->comment('Цвет иконки');
            $table->string('function_name', 100)->comment('Имя функции для открытия (например: openBanners)');
            $table->integer('sort_order')->nullable()->default(0)->comment('Порядок отображения');
            $table->boolean('enabled')->nullable()->default(true)->index('idx_enabled')->comment('Включен ли ярлык');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->boolean('show_on_desktop')->nullable()->default(true)->comment('Показывать на рабочем столе');
            $table->boolean('show_in_quick_access')->nullable()->default(false)->comment('Показывать в Быстром доступе (плитки)');

            $table->unique(['app_id', 'function_name'], 'uk_app_function');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_desktop_shortcuts');
    }
};
