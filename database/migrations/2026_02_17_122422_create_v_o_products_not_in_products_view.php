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
        DB::statement("CREATE VIEW `v_o_products_not_in_products` AS select `op`.`product_id` AS `product_id`,`op`.`offer_id` AS `offer_id`,`op`.`name` AS `name`,`op`.`barcode` AS `barcode` from (`sfera`.`o_products` `op` left join `sfera`.`products` `p` on(`op`.`offer_id` = `p`.`id`)) where `p`.`id` is null");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `v_o_products_not_in_products`");
    }
};
