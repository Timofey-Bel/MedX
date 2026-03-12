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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('login', 50)->unique('login');
            $table->string('password');
            $table->string('name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('role', 20)->nullable()->default('admin');
            $table->boolean('active')->nullable()->default(true);
            $table->dateTime('last_login')->nullable();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
