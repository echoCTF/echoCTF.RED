<?php

use yii\db\Migration;

/**
 * Class m231102_094132_create_tau_team_trigger
 */
class m231102_094132_create_tau_team_trigger extends Migration
{
  public $DROP_SQL = "DROP TRIGGER IF EXISTS {{%tau_team}}";
  public $CREATE_SQL = "CREATE TRIGGER {{%tau_team}} AFTER UPDATE ON {{%team}} FOR EACH ROW
    thisBegin:BEGIN
      DECLARE msg TEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET msg = 'Team details updated:';
      IF OLD.name != NEW.name THEN
        SET msg = CONCAT(msg,' name=',NEW.name);
      END IF;

      IF OLD.recruitment IS NOT NULL OR OLD.recruitment != NEW.recruitment THEN
        SET msg = CONCAT(msg,' recruitment=',NEW.recruitment);
      END IF;

      IF OLD.description != NEW.description THEN
        SET msg = CONCAT(msg,' description=',NEW.description);
      END IF;

      IF OLD.inviteonly != NEW.inviteonly THEN
        SET msg = CONCAT(msg,' inviteonly=',NEW.inviteonly);
      END IF;

      IF OLD.token != NEW.token THEN
        SET msg = CONCAT(msg,' token=',NEW.token);
      END IF;

      INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.id,NEW.owner_id,'update',msg);
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
