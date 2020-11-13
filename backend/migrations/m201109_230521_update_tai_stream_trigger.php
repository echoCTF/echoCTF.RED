<?php

use yii\db\Migration;

/**
 * Class m201109_230521_update_tai_stream_trigger
 */
class m201109_230521_update_tai_stream_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_stream}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_stream}} AFTER INSERT ON {{%stream}} FOR EACH ROW
  thisBegin:BEGIN
    DECLARE lteam_id INT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;

    INSERT INTO player_score (player_id,points) VALUES (NEW.player_id,NEW.points) ON DUPLICATE KEY UPDATE points=points+values(points);
    SELECT team_id INTO lteam_id FROM team_player WHERE player_id=NEW.player_id AND approved=1;
    IF lteam_id IS NOT NULL THEN
      INSERT IGNORE INTO team_stream (team_id,model,model_id,points,ts) VALUES (lteam_id,NEW.model,NEW.model_id,NEW.points,NEW.ts);
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
