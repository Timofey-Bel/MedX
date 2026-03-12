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
        DB::statement("CREATE VIEW `v_hashtags` AS select `ph`.`value` AS `value`,count(`ph`.`value`) AS `cnt` from `sfera`.`product_hashtags` `ph` group by `ph`.`value` order by count(`ph`.`value`) desc");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `v_hashtags`");
    }
};
