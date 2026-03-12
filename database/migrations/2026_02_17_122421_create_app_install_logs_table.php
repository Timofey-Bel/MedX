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
        Schema::create('app_install_logs', function (Blueprint $table) {
            $table->comment('Логи установки и обновления приложений');
            $table->integer('id', true);
            $table->string('app_id', 50)->index('idx_app_id')->comment('ID приложения');
            $table->enum('action', ['install', 'update', 'uninstall', 'rollback'])->index('idx_action')->comment('Действие');
            $table->enum('status', ['success', 'error', 'warning'])->comment('Статус');
            $table->text('message')->nullable()->comment('Сообщение');
            $table->json('log_data')->nullable()->comment('Подробный лог в JSON');
            $table->timestamp('created_at')->nullable()->useCurrent()->index('idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_install_logs');
    }
};
