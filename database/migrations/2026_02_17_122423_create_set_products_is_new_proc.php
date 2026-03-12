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
        DB::unprepared("CREATE DEFINER=`root`@`%` PROCEDURE `set_products_is_new`()
BEGIN

  UPDATE products
  SET is_new = CASE JSON_UNQUOTE(JSON_EXTRACT(attributes_json, '$.Новинка')) WHEN 'true' THEN 1 WHEN 'false' THEN 0 ELSE NULL END
  WHERE JSON_EXTRACT(attributes_json, '$.Новинка') IS NOT NULL;

END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS set_products_is_new");
    }
};
