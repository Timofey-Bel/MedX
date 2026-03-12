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
        DB::statement("CREATE VIEW `v_o_attributes` AS select `sfera`.`o_attributes`.`id` AS `id`,`sfera`.`o_attributes`.`product_id` AS `product_id`,`sfera`.`o_attributes`.`dictionary_value_id` AS `dictionary_value_id`,`sfera`.`o_attr_name`.`name` AS `name`,`sfera`.`o_attributes`.`value` AS `value` from (`sfera`.`o_attr_name` join `sfera`.`o_attributes` on(`sfera`.`o_attr_name`.`id` = `sfera`.`o_attributes`.`dictionary_value_id`))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `v_o_attributes`");
    }
};
