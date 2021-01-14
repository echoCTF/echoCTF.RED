<?php

use yii\db\Migration;

/**
 * Class m210112_211428_create_tbi_headshot_trigger
 */
class m210112_211428_create_tbi_headshot_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbi_headshot}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tbi_headshot}} BEFORE INSERT ON {{%headshot}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    IF (SELECT count(*) FROM headshot WHERE target_id=NEW.target_id)=0 THEN
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
