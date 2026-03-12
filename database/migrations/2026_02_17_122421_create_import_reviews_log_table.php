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
        Schema::create('import_reviews_log', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('last_id')->nullable()->comment('last_id из API для продолжения импорта');
            $table->integer('page_number')->nullable()->comment('Номер страницы');
            $table->integer('imported_count')->nullable()->default(0)->comment('Количество импортированных отзывов');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_reviews_log');
    }
};
