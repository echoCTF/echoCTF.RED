<?php

use yii\db\Migration;

/**
 * Class m231102_085411_create_tai_team_player_trigger
 */
class m231102_085411_create_tai_team_player_trigger extends Migration
{
  public $DROP_SQL = "DROP TRIGGER IF EXISTS {{%tai_team_player}}";
  public $CREATE_SQL = "CREATE TRIGGER {{%tai_team_player}} AFTER INSERT ON {{%team_player}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.team_id,NEW.player_id,'join','Player joined the team');
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
