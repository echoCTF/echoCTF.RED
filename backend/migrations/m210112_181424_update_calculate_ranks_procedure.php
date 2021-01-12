<?php

use yii\db\Migration;

/**
 * Class m210112_181424_update_calculate_ranks_procedure
 */
class m210112_181424_update_calculate_ranks_procedure extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%calculate_ranks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%calculate_ranks}} ()
  BEGIN
    CREATE TEMPORARY TABLE `ranking` (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY;
    START TRANSACTION;
      delete from player_rank;
      insert into ranking select NULL,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 and t2.status=10 order by points desc,t.ts asc, t.player_id asc;
      insert into player_rank select * from ranking;
    COMMIT;
    DROP TABLE `ranking`;
  END";
    // Use up()/down() to run migration code without a transaction.
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
