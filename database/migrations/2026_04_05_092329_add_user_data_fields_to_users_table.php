<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Данные календаря
            $table->json('visited_days')->nullable()->after('timezone');
            $table->date('first_visit_date')->nullable()->after('visited_days');
            $table->integer('freeze_count')->default(5)->after('first_visit_date');
            $table->json('used_freezes')->nullable()->after('freeze_count');
            
            // Достижения
            $table->json('achievements')->nullable()->after('used_freezes');
            
            // Помодоро
            $table->json('pomodoro_state')->nullable()->after('achievements');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'visited_days',
                'first_visit_date',
                'freeze_count',
                'used_freezes',
                'achievements',
                'pomodoro_state'
            ]);
        });
    }
};
