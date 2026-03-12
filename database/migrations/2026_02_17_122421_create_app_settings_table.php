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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->comment('Настройки установленных приложений');
            $table->integer('id', true);
            $table->string('app_id', 50)->comment('ID приложения');
            $table->string('setting_key', 100)->comment('Ключ настройки');
            $table->text('setting_value')->nullable()->comment('Значение настройки');
            $table->enum('setting_type', ['string', 'number', 'boolean', 'json'])->nullable()->default('string')->comment('Тип значения');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['app_id', 'setting_key'], 'uk_app_setting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
