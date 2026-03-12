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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('order_code')->nullable();
            $table->dateTime('date_init')->nullable()->useCurrent();
            $table->integer('status')->nullable()->default(1);
            $table->float('full_sum')->nullable();
            $table->float('discount_sum')->nullable();
            $table->float('pay_sum')->nullable();
            $table->float('bonus')->nullable();
            $table->float('cart_weight')->nullable();
            $table->float('cart_volume')->nullable();
            $table->float('cart_density')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->longText('comment_user')->nullable();
            $table->string('tracking_id')->nullable();
            $table->string('checkoutOrderId')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('user_role')->nullable();
            $table->string('user_card_code')->nullable();
            $table->string('ip', 150)->nullable();
            $table->string('user_agent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
