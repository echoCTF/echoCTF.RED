<?php

use yii\db\Migration;

/**
 * Class m230827_161324_update_tad_target_instance_trigger_add_team_allowed
 */
class m230827_161324_update_tad_target_instance_trigger_add_team_allowed extends Migration
{
  public $DROP_SQL = "DROP TRIGGER IF EXISTS {{%tad_target_instance}}";
  public $CREATE_SQL = "CREATE TRIGGER {{%tad_target_instance}} AFTER DELETE ON {{%target_instance}} FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      IF OLD.ip IS NOT NULL THEN
        DO memc_delete(CONCAT('target:',OLD.ip));
      END IF;
      INSERT DELAYED INTO {{%target_instance_audit}} (op,player_id,target_id,server_id,ip,reboot,team_allowed,ts) VALUES ('d',OLD.player_id,OLD.target_id,OLD.server_id,OLD.ip,OLD.reboot,OLD.team_allowed,NOW());
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
