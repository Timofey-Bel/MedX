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
        Schema::create('installed_apps', function (Blueprint $table) {
            $table->comment('Установленные приложения в системе');
            $table->integer('id', true);
            $table->string('app_id', 50)->unique('uk_app_id')->comment('Уникальный ID приложения');
            $table->string('name')->comment('Название приложения');
            $table->text('description')->nullable()->comment('Описание приложения');
            $table->string('version', 20)->comment('Версия приложения');
            $table->string('author')->nullable()->comment('Автор приложения');
            $table->string('icon')->nullable()->comment('Иконка приложения (material icon или путь)');
            $table->string('icon_color', 20)->nullable()->comment('Цвет иконки');
            $table->string('category', 50)->nullable()->index('idx_category')->comment('Категория (content, system, tools)');
            $table->enum('status', ['active', 'inactive', 'error'])->nullable()->default('active')->index('idx_status')->comment('Статус приложения');
            $table->timestamp('installed_at')->nullable()->useCurrent()->comment('Дата установки');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Дата обновления');
            $table->string('package_path', 500)->nullable()->comment('Путь к пакету установки');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installed_apps');
    }
};
