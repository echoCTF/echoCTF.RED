<?php

use yii\db\Migration;

class m260911_170855_create_procedure_set_multi extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%set_multi}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%set_multi}}(IN kv_string TEXT, IN delim VARCHAR(10))
BEGIN
  DECLARE remaining TEXT DEFAULT kv_string;
  DECLARE pair TEXT;
  DECLARE k VARCHAR(255);
  DECLARE v VARCHAR(255);
  DECLARE pos INT;

  SET delim = IFNULL(delim, ',');

  WHILE LENGTH(remaining) > 0 DO
    SET pos = LOCATE(delim, remaining);
    IF pos > 0 THEN
      SET pair = SUBSTRING(remaining, 1, pos - 1);
      SET remaining = SUBSTRING(remaining, pos + LENGTH(delim));
    ELSE
      SET pair = remaining;
      SET remaining = '';
    END IF;

    SET k = TRIM(SUBSTRING_INDEX(pair, '=>', 1));
    SET v = TRIM(SUBSTRING_INDEX(pair, '=>', -1));

    DO memc_set(k, v);
  END WHILE;
END";

  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }
}