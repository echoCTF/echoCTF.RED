<?php

use yii\db\Migration;

/**
 * Class m241030_083703_update_tau_player_score_non_negative_points
 */
class m241030_083703_update_tau_player_score_non_negative_points extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player_score}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tau_player_score}} AFTER UPDATE ON {{%player_score}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;

    IF NEW.points>=OLD.points THEN
      INSERT INTO player_score_monthly (player_id, points, dated_at) VALUES (NEW.player_id, ABS(ifnull(OLD.points,0)-NEW.points), EXTRACT(YEAR_MONTH FROM NOW())) ON DUPLICATE KEY UPDATE points=points+values(points);
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
