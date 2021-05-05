<?php

use yii\db\Migration;

/**
 * Class m210430_155141_create_target_solved_percentage_function
 */
class m210430_155141_create_target_solved_percentage_function extends Migration
{
  public $DROP_SQL="DROP FUNCTION IF EXISTS {{%target_solved_percentage}}";
  public $CREATE_SQL="CREATE FUNCTION {{%target_solved_percentage}}(n INT UNSIGNED) returns float READS SQL DATA
  BEGIN
  DECLARE average_pct float;
  SET average_pct=(select ((select count(*) from headshot where target_id=n)*100)/count(distinct player_id) from stream where (model='finding' and model_id in (select id from finding where target_id=n)) or (model='treasure' and model_id in (select id from treasure where target_id=n)));
  RETURN average_pct;
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
