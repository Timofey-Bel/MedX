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
        Schema::create('admin_role_permissions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('role_id');
            $table->integer('permission_id')->index('admin_role_permissions_ibfk_2');
            $table->boolean('granted')->nullable()->default(true);

            $table->unique(['role_id', 'permission_id'], 'unique_role_permission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_role_permissions');
    }
};
