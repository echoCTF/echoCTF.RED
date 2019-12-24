<?php

use yii\db\Migration;

/**
 * Class m191101_172735_create_update_player_ranks_event
 */
class m191101_172735_create_update_player_ranks_event extends Migration
{
      public $DROP_SQL="DROP EVENT IF EXISTS {{%update_player_ranks}}";

      public function up()
      {
        $CREATE_SQL="CREATE EVENT {{%update_player_ranks}} ON SCHEDULE EVERY 10 MINUTE DO
    BEGIN
        SET @curRank=0;
        REPLACE INTO player_rank (id,player_id) select @curRank:=@curRank+1 AS rank,t.player_id from player_score as t order by points desc,t.player_id;
    END
    ";
          $this->db->createCommand($this->DROP_SQL)->execute();
          $this->db->createCommand($CREATE_SQL)->execute();


      }

      public function down()
      {
        $this->db->createCommand($this->DROP_SQL)->execute();
      }
  }
