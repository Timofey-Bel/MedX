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
        DB::unprepared("CREATE DEFINER=`root`@`%` PROCEDURE `sp_import_hashtags_from_o_attributes`()
BEGIN
  DECLARE done int DEFAULT FALSE;
  DECLARE v_product_id varchar(50);
  DECLARE v_value text;
  DECLARE v_hashtag varchar(255);
  DECLARE v_position int;
  DECLARE v_remaining_text text;
  DECLARE v_total_records int DEFAULT 0;
  DECLARE v_total_hashtags int DEFAULT 0;
  DECLARE v_errors int DEFAULT 0;

  
  DECLARE cur CURSOR FOR
  SELECT
    product_id,
    value
  FROM o_attributes
  WHERE dictionary_value_id = 23171
  AND value IS NOT NULL
  AND value != '';

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  
  START TRANSACTION;

    
    SELECT
      CONCAT('=== Starting hashtags import at ', NOW(), ' ===') AS log_message;

    OPEN cur;

      read_loop:
    LOOP
      FETCH cur INTO v_product_id, v_value;

      IF done THEN
        LEAVE read_loop;
      END IF;

      SET v_total_records = v_total_records + 1;
      SET v_remaining_text = TRIM(v_value);

        
        hashtag_loop:
      WHILE LENGTH(v_remaining_text) > 0 DO
        
        SET v_position = LOCATE(' ', v_remaining_text);

        IF v_position > 0 THEN
          
          SET v_hashtag = TRIM(SUBSTRING(v_remaining_text, 1, v_position - 1));
          
          SET v_remaining_text = TRIM(SUBSTRING(v_remaining_text, v_position + 1));
        ELSE
          
          SET v_hashtag = TRIM(v_remaining_text);
          SET v_remaining_text = '';
        END IF;

        
        IF LENGTH(v_hashtag) > 1
          AND LEFT(v_hashtag, 1) = '#' THEN
        
        BEGIN
          DECLARE CONTINUE HANDLER FOR 1062 
          BEGIN
          
          END;

          INSERT INTO product_hashtags (product_id, value)
            VALUES (v_product_id, v_hashtag);

          SET v_total_hashtags = v_total_hashtags + 1;
        END;
        END IF;

      END WHILE hashtag_loop;

      
      IF v_total_records MOD 100 = 0 THEN
        SELECT
          CONCAT('Processed ', v_total_records, ' products, inserted ', v_total_hashtags, ' hashtags...') AS progress;
      END IF;

    END LOOP read_loop;

    CLOSE cur;

  
  COMMIT;

  
  SELECT
    CONCAT('=== Import completed at ', NOW(), ' ===') AS log_message,
    v_total_records AS total_products_processed,
    v_total_hashtags AS total_hashtags_inserted,
    (SELECT
        COUNT(*)
      FROM product_hashtags) AS total_hashtags_in_table,
    (SELECT
        COUNT(DISTINCT product_id)
      FROM product_hashtags) AS total_products_with_hashtags;

END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_import_hashtags_from_o_attributes");
    }
};
