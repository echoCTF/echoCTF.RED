<?php

use yii\db\Migration;

/**
 * Class m231101_114257_update_tad_team_trigger_fix_order
 */
class m231101_114257_update_tad_team_trigger_fix_order extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_team}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_team}} AFTER DELETE ON {{%team}} FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      DELETE FROM {{%team_stream}} WHERE team_id=OLD.id;
      DELETE FROM {{%team_rank}} WHERE team_id=OLD.id;
      DELETE FROM {{%team_score}} WHERE team_id=OLD.id;
      INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (OLD.id,OLD.owner_id,'delete',CONCAT('Team ',OLD.name,' deleted'));
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
