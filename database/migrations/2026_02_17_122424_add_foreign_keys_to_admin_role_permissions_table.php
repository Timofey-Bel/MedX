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
        Schema::table('admin_role_permissions', function (Blueprint $table) {
            $table->foreign(['role_id'], 'admin_role_permissions_ibfk_1')->references(['id'])->on('admin_roles')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['permission_id'], 'admin_role_permissions_ibfk_2')->references(['id'])->on('admin_permissions')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_role_permissions', function (Blueprint $table) {
            $table->dropForeign('admin_role_permissions_ibfk_1');
            $table->dropForeign('admin_role_permissions_ibfk_2');
        });
    }
};
