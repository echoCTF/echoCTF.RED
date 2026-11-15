<?php

use yii\db\Migration;

class m261114_143010_create_procedure_add_player_streak extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%add_player_streak}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%add_player_streak}}(IN usid INT UNSIGNED, IN claim_ts DATETIME)
BEGIN
  DECLARE wk DATE DEFAULT DATE(claim_ts) - INTERVAL WEEKDAY(claim_ts) DAY;
  INSERT INTO player_streak (player_id,current,best,last_week) VALUES (usid,1,1,wk)
    ON DUPLICATE KEY UPDATE
      current=IF(last_week>=wk, current, IF(last_week=wk - INTERVAL 7 DAY, current+1, 1)),
      best=GREATEST(best,current),
      last_week=GREATEST(last_week,wk);
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
