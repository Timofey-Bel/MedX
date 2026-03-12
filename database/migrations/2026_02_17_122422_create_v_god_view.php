<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE VIEW `v_god` AS select `a`.`value` AS `value`,count(`a`.`value`) AS `cnt` from `sfera`.`attributes` `a` where `a`.`name` = 'Год' group by `a`.`value`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `v_god`");
    }
};
