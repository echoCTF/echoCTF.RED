<?php

use yii\db\Migration;

/**
 * Class m210114_095711_create_tbi_challenge_solver_trigger
 */
class m210114_095711_create_tbi_challenge_solver_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbi_challenge_solver}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tbi_challenge_solver}} BEFORE INSERT ON {{%challenge_solver}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    IF (SELECT count(*) FROM challenge_solver WHERE challenge_id=NEW.challenge_id)=0 THEN
      SET NEW.first=1;
    END IF;
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
