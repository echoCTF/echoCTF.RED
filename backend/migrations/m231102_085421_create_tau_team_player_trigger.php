<?php

use yii\db\Migration;

/**
 * Class m231102_085421_create_tau_team_player_trigger
 */
class m231102_085421_create_tau_team_player_trigger extends Migration
{
  public $DROP_SQL = "DROP TRIGGER IF EXISTS {{%tau_team_player}}";
  public $CREATE_SQL = "CREATE TRIGGER {{%tau_team_player}} AFTER UPDATE ON {{%team_player}} FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      IF OLD.approved != NEW.approved THEN
        IF NEW.approved = 0 THEN
          INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.team_id,NEW.player_id,'reject','Player membership rejected');
        ELSE
          INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.team_id,NEW.player_id,'approve','Player membership approved');
        END IF;
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
