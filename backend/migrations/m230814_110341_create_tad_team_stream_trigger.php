<?php

use yii\db\Migration;

/**
 * Class m230814_110341_create_tad_team_stream_trigger
 */
class m230814_110341_create_tad_team_stream_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_team_stream}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_team_stream}} AFTER DELETE ON {{%team_stream}} FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      INSERT INTO {{%team_score}} ({{%team_id}},{{%points}},{{%ts}}) VALUES (OLD.team_id,-OLD.points,OLD.ts) ON DUPLICATE KEY UPDATE points=if(points+values(points)<0,0,points+values(points)),ts=values(ts);
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
