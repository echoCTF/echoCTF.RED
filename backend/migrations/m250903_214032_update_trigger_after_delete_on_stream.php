<?php

use yii\db\Migration;

class m250903_214032_update_trigger_after_delete_on_stream extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_stream}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tad_stream}} AFTER DELETE ON {{%stream}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    INSERT INTO player_score (player_id,points) VALUES (OLD.player_id,-OLD.points) ON DUPLICATE KEY UPDATE points=if(points+values(points)<0,0,points+values(points));
    DELETE FROM team_stream where stream_id=OLD.id;
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