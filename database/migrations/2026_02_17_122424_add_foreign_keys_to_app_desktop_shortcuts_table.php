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
        Schema::table('app_desktop_shortcuts', function (Blueprint $table) {
            $table->foreign(['app_id'], 'fk_desktop_shortcuts_app')->references(['app_id'])->on('installed_apps')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_desktop_shortcuts', function (Blueprint $table) {
            $table->dropForeign('fk_desktop_shortcuts_app');
        });
    }
};
