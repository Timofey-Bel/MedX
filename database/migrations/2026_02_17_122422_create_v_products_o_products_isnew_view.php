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
        DB::statement("CREATE VIEW `v_products_o_products_isnew` AS select `op`.`product_id` AS `product_id`,`op`.`offer_id` AS `offer_id`,`op`.`sku` AS `sku` from (`sfera`.`o_products` `op` join `sfera`.`products` `p` on(`op`.`offer_id` = `p`.`id`)) where `p`.`is_new` = 1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `v_products_o_products_isnew`");
    }
};
