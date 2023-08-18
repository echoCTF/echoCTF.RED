<?php

use yii\db\Migration;

/**
 * Class m230813_220358_create_tad_stream_trigger
 */
class m230813_220358_create_tad_stream_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_stream}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_stream}} AFTER DELETE ON {{%stream}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    INSERT INTO player_score (player_id,points) VALUES (OLD.player_id,-OLD.points) ON DUPLICATE KEY UPDATE points=if(points+values(points)<0,0,points+values(points));
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
