<?php

use yii\db\Migration;

/**
 * Class m210430_154641_create_target_started_counter_function
 */
class m210430_154641_create_target_started_counter_function extends Migration
{
  public $DROP_SQL="DROP FUNCTION IF EXISTS {{%target_started_count}}";
  public $CREATE_SQL="CREATE FUNCTION {{%target_started_count}}(n INT UNSIGNED) returns INT READS SQL DATA
BEGIN
DECLARE counter INT;
SET counter=(select count(distinct player_id) from stream where (model='finding' and model_id in (select id from finding where target_id=n)) or (model='treasure' and model_id in (select id from treasure where target_id=n)));
RETURN counter;
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
