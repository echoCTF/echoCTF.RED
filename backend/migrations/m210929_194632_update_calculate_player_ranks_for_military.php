<?php

use yii\db\Migration;

/**
 * Class m210929_194632_update_calculate_player_ranks_for_military
 */
class m210929_194632_update_calculate_player_ranks_for_military extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%calculate_ranks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%calculate_ranks}} ()
  BEGIN
    CREATE TEMPORARY TABLE `cr_ranking` (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    START TRANSACTION;
      delete from player_rank where player_id in (select id from player where academic=1);
      insert into cr_ranking select NULL,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 and t2.status=10 and t2.academic=1 order by points desc,t.ts asc, t.player_id asc;
      insert into player_rank select * from cr_ranking;
    COMMIT;
    DROP TABLE `cr_ranking`;

    CREATE TEMPORARY TABLE `cr_ranking` (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    START TRANSACTION;
      delete from player_rank where player_id in (select id from player where academic=0);
      insert into cr_ranking select NULL,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 and t2.status=10 and t2.academic=0 order by points desc,t.ts asc, t.player_id asc;
      insert into player_rank select * from cr_ranking;
    COMMIT;
    DROP TABLE `cr_ranking`;

    CREATE TEMPORARY TABLE `cr_ranking` (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    START TRANSACTION;
      delete from player_rank where player_id in (select id from player where academic=2);
      insert into cr_ranking select NULL,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 and t2.status=10 and t2.academic=2 order by points desc,t.ts asc, t.player_id asc;
      insert into player_rank select * from cr_ranking;
    COMMIT;
    DROP TABLE `cr_ranking`;

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
