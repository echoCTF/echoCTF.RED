<?php

use yii\db\Migration;

/**
 * Class m220515_124644_create_tad_target_ondemand_trigger
 */
class m220515_124644_create_tad_target_ondemand_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_target_ondemand}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_target_ondemand}} AFTER DELETE ON {{%target_ondemand}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET on_ondemand=0,ondemand_state=-1 WHERE id=OLD.target_id;
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
