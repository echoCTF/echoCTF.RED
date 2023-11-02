<?php

use yii\db\Migration;

/**
 * Class m231102_093631_create_tai_team_trigger
 */
class m231102_093631_create_tai_team_trigger extends Migration
{
  public $DROP_SQL = "DROP TRIGGER IF EXISTS {{%tai_team}}";
  public $CREATE_SQL = "CREATE TRIGGER {{%tai_team}} AFTER INSERT ON {{%team}} FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.id,NEW.owner_id,'create',CONCAT('Team ',NEW.name,' created'));
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
