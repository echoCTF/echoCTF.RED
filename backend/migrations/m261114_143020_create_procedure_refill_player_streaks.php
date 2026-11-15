<?php

use yii\db\Migration;

class m261114_143020_create_procedure_refill_player_streaks extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%refill_player_streaks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%refill_player_streaks}}()
BEGIN
  START TRANSACTION;
  DELETE FROM player_streak;
  -- consecutive weeks share the same grp, current is the length of the latest run
  INSERT INTO player_streak (player_id,current,best,last_week)
  SELECT player_id, MAX(IF(rn=1,len,0)), MAX(len), MAX(end_wk) FROM (
    SELECT player_id, COUNT(*) AS len, MAX(wk) AS end_wk, ROW_NUMBER() OVER (PARTITION BY player_id ORDER BY MAX(wk) DESC) AS rn FROM (
      SELECT player_id, wk, TO_DAYS(wk) DIV 7 - ROW_NUMBER() OVER (PARTITION BY player_id ORDER BY wk) AS grp FROM (
        SELECT player_id, DATE(ts) - INTERVAL WEEKDAY(ts) DAY AS wk FROM player_finding
        UNION
        SELECT player_id, DATE(ts) - INTERVAL WEEKDAY(ts) DAY AS wk FROM player_treasure
      ) AS weeks
    ) AS grouped GROUP BY player_id, grp
  ) AS runs GROUP BY player_id;
  COMMIT;
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
