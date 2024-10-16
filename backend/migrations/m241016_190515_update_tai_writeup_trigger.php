<?php

use yii\db\Migration;

/**
 * Class m241016_190515_update_tai_writeup_trigger
 */
class m241016_190515_update_tai_writeup_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_writeup}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_writeup}} AFTER INSERT ON {{%writeup}} FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
    UPDATE target_state SET total_writeups=total_writeups+1, approved_writeups=approved_writeups+IF(NEW.approved>0,1,0) WHERE id=NEW.target_id;
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
