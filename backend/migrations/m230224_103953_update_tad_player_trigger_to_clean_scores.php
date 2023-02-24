<?php

use yii\db\Migration;

/**
 * Class m230224_103953_update_tad_player_trigger_to_clean_scores
 */
class m230224_103953_update_tad_player_trigger_to_clean_scores extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_player}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_player}} AFTER DELETE ON {{%player}} FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;

      IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
      END IF;
      SELECT memc_delete(CONCAT('player_type:',OLD.id)) INTO @devnull;
      SELECT memc_delete(CONCAT('player:',OLD.id)) INTO @devnull;
      SELECT memc_delete(CONCAT('team_player:',OLD.id)) INTO @devnull;
      DELETE FROM player_score WHERE player_id=OLD.id;
      DELETE FROM player_score_monthly WHERE player_id=OLD.id;
      DELETE FROM player_counter_nf WHERE player_id=OLD.id;
      DELETE FROM profile WHERE player_id=OLD.id;
      DELETE FROM player_last WHERE id=OLD.id;
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
