<?php

use yii\db\Migration;

class m250903_211309_update_trigger_after_insert_on_stream extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_stream}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_stream}} AFTER INSERT ON {{%stream}} FOR EACH ROW
    thisBegin:BEGIN
    DECLARE lteam_id INT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    IF NEW.points>0 THEN
      INSERT INTO player_score (player_id,points) VALUES (NEW.player_id,NEW.points) ON DUPLICATE KEY UPDATE points=points+values(points);
    END IF;
    SELECT team_id INTO lteam_id FROM team_player WHERE player_id=NEW.player_id AND approved=1;
    IF lteam_id IS NOT NULL THEN
      INSERT IGNORE INTO team_stream (stream_id,player_id,team_id,model,model_id,points,ts) VALUES (NEW.id,NEW.player_id,lteam_id,NEW.model,NEW.model_id,NEW.points,NEW.ts);
    END IF;
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