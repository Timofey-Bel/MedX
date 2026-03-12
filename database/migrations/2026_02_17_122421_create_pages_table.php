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
        Schema::create('pages', function (Blueprint $table) {
            $table->comment('Страницы сайта с иерархической структурой');
            $table->integer('id', true);
            $table->boolean('active')->nullable()->default(true)->comment('1 = активна, 0 = неактивна');
            $table->string('name');
            $table->string('title')->nullable();
            $table->integer('parent_id')->nullable()->default(0);
            $table->text('content')->nullable()->comment('Содержимое страницы (HTML)');
            $table->integer('sort')->nullable()->default(0)->comment('Порядок сортировки');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
