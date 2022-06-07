<?php

use yii\db\Migration;

/**
 * Class m220607_093340_create_time_headshot_with_player
 */
class m220607_093340_create_time_headshot_with_player extends Migration
{
    public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%time_headshot_with_player}}";
    public $CREATE_SQL="CREATE PROCEDURE {{%time_headshot_with_player}} (IN pid INT, IN tid INT, IN aspid INT)
    BEGIN
      DECLARE min_finding,min_treasure,max_finding,max_treasure, max_val, min_val DATETIME;
      SELECT min(ts),max(ts) INTO min_finding,max_finding FROM player_finding WHERE player_id IN (pid, aspid) AND finding_id IN (SELECT id FROM finding WHERE target_id=tid);
      SELECT min(ts),max(ts) INTO min_treasure,max_treasure FROM player_treasure WHERE player_id IN (pid,aspid) AND treasure_id IN (SELECT id FROM treasure WHERE target_id=tid);
      SELECT GREATEST(max_finding, max_treasure), LEAST(min_finding, min_treasure) INTO max_val,min_val;
      UPDATE {{%headshot}} SET timer=UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val) WHERE player_id=pid AND target_id=tid;
    END";
      // Use up()/down() to run migration code without a transaction.
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
