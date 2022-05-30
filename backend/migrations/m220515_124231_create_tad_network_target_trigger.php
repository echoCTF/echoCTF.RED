<?php

use yii\db\Migration;

/**
 * Class m220515_124231_create_tad_network_target_trigger
 */
class m220515_124231_create_tad_network_target_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_network_target}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_network_target}} AFTER DELETE ON {{%network_target}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET on_network=ifnull((select 1 from network_target where target_id=OLD.target_id),0) WHERE id=OLD.target_id;
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
