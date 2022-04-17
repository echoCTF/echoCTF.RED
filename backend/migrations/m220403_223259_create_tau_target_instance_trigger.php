<?php

use yii\db\Migration;

/**
 * Class m220403_223259_create_tau_target_instance_trigger
 */
class m220403_223259_create_tau_target_instance_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_target_instance}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tau_target_instance}} AFTER UPDATE ON {{%target_instance}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    IF NEW.ip IS NOT NULL AND OLD.ip IS NULL THEN
      DO memc_set(CONCAT('target:',NEW.ip),NEW.target_id);
    ELSEIF (NEW.ip IS NULL OR NEW.ip = '') and OLD.ip IS NOT NULL THEN
        DO memc_delete(CONCAT('target:',OLD.ip));
    ELSEIF NEW.ip!=OLD.ip THEN
      DO memc_delete(CONCAT('target:',OLD.ip));
      DO memc_set(CONCAT('target:',NEW.ip),NEW.target_id);
    END IF;
    INSERT DELAYED INTO {{%target_instance_audit}} (op,player_id,target_id,server_id,ip,reboot,ts) VALUES ('u',NEW.player_id,NEW.target_id,NEW.server_id,NEW.ip,NEW.reboot,NOW());
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
