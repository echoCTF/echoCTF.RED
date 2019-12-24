<?php

use yii\db\Migration;

/**
 * Class m191030_070539_create_tad_player_trigger
 */
class m191030_070539_create_tad_player_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_player}}";

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

      $CREATE_SQL="CREATE TRIGGER {{%tad_player}} AFTER DELETE ON {{%player}} FOR EACH ROW
      BEGIN
        IF (select memc_server_count()<1) THEN
          select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
        END IF;
        SELECT memc_delete(CONCAT('player_type:',OLD.id)) INTO @devnull;
        SELECT memc_delete(CONCAT('player:',OLD.id)) INTO @devnull;
        SELECT memc_delete(CONCAT('team_player:',OLD.id)) INTO @devnull;
        DELETE FROM player_score WHERE player_id=OLD.id;
        DELETE FROM profile WHERE player_id=OLD.id;
        DELETE FROM player_last WHERE id=OLD.id;
      END";
        $this->db->createCommand($this->DROP_SQL)->execute();
        $this->db->createCommand($CREATE_SQL)->execute();
    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
    }
}
