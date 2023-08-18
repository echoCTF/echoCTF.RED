<?php

use yii\db\Migration;

/**
 * Class m230729_200303_update_tad_player_delete_team_data
 */
class m230729_200303_update_tbd_player_delete_team_data extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbd_player}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tbd_player}} BEFORE DELETE ON {{%player}} FOR EACH ROW
    thisBegin:BEGIN
      DECLARE tid INT default 0;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SELECT id INTO tid FROM team where owner_id=OLD.id;
      IF tid > 0 THEN
        DELETE FROM team_score WHERE team_id=tid;
      END IF;
      DELETE FROM player_ssl WHERE player_id=OLD.id;
      DELETE FROM player_rank WHERE player_id=OLD.id;
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
