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
        Schema::create('hello_world_content', function (Blueprint $table) {
            $table->comment('Хранение редактируемого текста для примера Hello World');
            $table->integer('id', true);
            $table->text('content')->default('Hello world!');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hello_world_content');
    }
};
