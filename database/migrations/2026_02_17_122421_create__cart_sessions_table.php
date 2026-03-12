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
        Schema::create('_cart_sessions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('session_id')->unique('session_id');
            $table->longText('session')->nullable()->comment('JSON данные корзины');
            $table->string('ip', 45)->nullable()->index('ip');
            $table->integer('cart_amount')->nullable()->default(0)->comment('Общее количество товаров');
            $table->decimal('cart_sum', 10)->nullable()->default(0)->comment('Общая сумма корзины');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_cart_sessions');
    }
};
