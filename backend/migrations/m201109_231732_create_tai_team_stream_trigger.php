<?php

use yii\db\Migration;

/**
 * Class m201109_231732_create_tai_team_stream_trigger
 */
class m201109_231732_create_tai_team_stream_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_team_stream}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_team_stream}} AFTER INSERT ON {{%team_stream}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    INSERT INTO {{%team_score}} ({{%team_id}},{{%points}},{{%ts}}) VALUES (NEW.team_id,NEW.points,NEW.ts) ON DUPLICATE KEY UPDATE points=points+values(points),ts=values(ts);
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
