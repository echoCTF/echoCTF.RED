<?php

use yii\db\Migration;

/**
 * Class m191218_083102_update_player_rank_event_order
 */
class m191218_083102_update_player_rank_event_order extends Migration
{
  public $DROP_SQL="DROP EVENT IF EXISTS {{%update_player_ranks}}";

  public function up()
  {
        $CREATE_SQL="CREATE PROCEDURE calculate_ranks()
BEGIN
START TRANSACTION;
  SET @curRank=0;
  REPLACE INTO player_rank (id,player_id) select @curRank:=@curRank+1 AS rank,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 order by points desc,t.ts asc, t.player_id asc;
  COMMIT;
END
";
    $this->db->createCommand('DROP PROCEDURE IF EXISTS calculate_ranks')->execute();
    $this->db->createCommand($CREATE_SQL)->execute();

    $CREATE_SQL="CREATE EVENT {{%update_player_ranks}} ON SCHEDULE EVERY 10 MINUTE DO
BEGIN
 call calculate_ranks();
END
";

      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($CREATE_SQL)->execute();
      $this->db->createCommand('TRUNCATE player_rank')->execute();

  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }

}
