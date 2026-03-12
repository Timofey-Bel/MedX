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
        Schema::create('app_routes', function (Blueprint $table) {
            $table->comment('Маршруты установленных приложений');
            $table->integer('id', true);
            $table->string('app_id', 50)->index('idx_app_id')->comment('ID приложения');
            $table->enum('route_type', ['admin', 'public'])->nullable()->default('admin')->comment('Тип маршрута');
            $table->string('route_path')->comment('Путь маршрута (например: banners)');
            $table->string('module_path', 500)->comment('Путь к модулю (например: admin/banners)');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['route_type', 'route_path'], 'uk_route');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_routes');
    }
};
