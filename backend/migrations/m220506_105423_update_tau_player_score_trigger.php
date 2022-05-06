<?php

use yii\db\Migration;

/**
 * Class m220506_105423_update_tau_player_score_trigger
 */
class m220506_105423_update_tau_player_score_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player_score}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tau_player_score}} AFTER UPDATE ON {{%player_score}} FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      INSERT INTO player_score_monthly (player_id, points, dated_at) VALUES (NEW.player_id, ABS(ifnull(OLD.points,0)-NEW.points), EXTRACT(YEAR_MONTH FROM NOW())) ON DUPLICATE KEY UPDATE points=points+values(points);
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
