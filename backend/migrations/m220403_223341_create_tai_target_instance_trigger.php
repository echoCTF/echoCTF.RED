<?php

use yii\db\Migration;

/**
 * Class m220403_223341_create_tai_target_instance_trigger
 */
class m220403_223341_create_tai_target_instance_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_target_instance}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_target_instance}} AFTER INSERT ON {{%target_instance}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    IF NEW.ip IS NOT NULL THEN
      DO memc_set(CONCAT('target:',NEW.ip),NEW.target_id);
    END IF;
    INSERT DELAYED INTO {{%target_instance_audit}} (op,player_id,target_id,server_id,ip,reboot,ts) VALUES ('i',NEW.player_id,NEW.target_id,NEW.server_id,NEW.ip,NEW.reboot,NOW());
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
