<?php

use yii\db\Migration;

/**
 * Class m220403_223247_create_tad_target_instance_trigger
 */
class m220403_223247_create_tad_target_instance_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_target_instance}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tad_target_instance}} AFTER DELETE ON {{%target_instance}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    IF OLD.ip IS NOT NULL THEN
      DO memc_delete(CONCAT('target:',OLD.ip));
    END IF;
    INSERT DELAYED INTO {{%target_instance_audit}} (op,player_id,target_id,server_id,ip,reboot,ts) VALUES ('d',OLD.player_id,OLD.target_id,OLD.server_id,OLD.ip,OLD.reboot,NOW());
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
