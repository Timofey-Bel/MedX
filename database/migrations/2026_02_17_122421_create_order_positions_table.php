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
        Schema::create('order_positions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->dateTime('created')->nullable()->useCurrent();
            $table->bigInteger('order_num')->nullable();
            $table->string('order_code', 25)->nullable();
            $table->integer('pieces')->nullable();
            $table->integer('min')->nullable();
            $table->decimal('bill', 19)->nullable();
            $table->decimal('cost', 19)->nullable();
            $table->decimal('piece_cost', 19)->nullable();
            $table->decimal('amount', 8, 3)->nullable();
            $table->decimal('sum', 19)->nullable();
            $table->string('art', 25)->nullable();
            $table->string('guid', 25)->nullable();
            $table->string('title')->nullable();
            $table->string('model', 150)->nullable();
            $table->float('weight')->nullable();
            $table->float('w')->nullable()->comment('ширина в упаковке');
            $table->float('l')->nullable()->comment('длина в упаковке');
            $table->float('h')->nullable()->comment('высота в упаковке');
            $table->float('volume')->nullable()->comment('объем товара в упаковке');
            $table->float('piece_weight')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_positions');
    }
};
