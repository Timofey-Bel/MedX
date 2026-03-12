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
        Schema::create('sms_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone', 20)->nullable()->index('phone');
            $table->string('email')->nullable();
            $table->string('type', 10)->default('phone');
            $table->string('code', 4)->index('code');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_codes');
    }
};
