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
        DB::statement("CREATE VIEW `v_o_product_categories` AS select `op`.`description_category_id` AS `description_category_id`,`op`.`type_id` AS `type_id`,count(0) AS `cnt` from `sfera`.`o_products` `op` group by `op`.`description_category_id` order by count(0) desc");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `v_o_product_categories`");
    }
};
