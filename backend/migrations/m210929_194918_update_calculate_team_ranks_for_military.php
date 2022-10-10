<?php

use yii\db\Migration;

/**
 * Class m210929_194918_update_calculate_team_ranks_for_military
 */
class m210929_194918_update_calculate_team_ranks_for_military extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%calculate_team_ranks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%calculate_team_ranks}} ()
  BEGIN
    IF (SELECT count(*) FROM sysconfig WHERE id='teams')>0 AND (SELECT val FROM sysconfig WHERE id='teams')=1 THEN
      CREATE TEMPORARY TABLE `tr_ranking` (id int primary key AUTO_INCREMENT,team_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
      START TRANSACTION;
        insert into tr_ranking select NULL,t.team_id from team_score as t left join team as t2 on t.team_id=t2.id WHERE t2.academic=1 order by points desc,t.ts asc, t.team_id asc;
        insert into team_rank select * from tr_ranking ON DUPLICATE KEY UPDATE id=values(id);
      COMMIT;
      DROP TABLE `tr_ranking`;

      CREATE TEMPORARY TABLE `tr_ranking` (id int primary key AUTO_INCREMENT,team_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
      START TRANSACTION;
        insert into tr_ranking select NULL,t.team_id from team_score as t left join team as t2 on t.team_id=t2.id WHERE t2.academic=0 order by points desc,t.ts asc, t.team_id asc;
        insert into team_rank select * from tr_ranking ON DUPLICATE KEY UPDATE id=values(id);
      COMMIT;
      DROP TABLE `tr_ranking`;

      CREATE TEMPORARY TABLE `tr_ranking` (id int primary key AUTO_INCREMENT,team_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
      START TRANSACTION;
        insert into tr_ranking select NULL,t.team_id from team_score as t left join team as t2 on t.team_id=t2.id WHERE t2.academic=2 order by points desc,t.ts asc, t.team_id asc;
        insert into team_rank select * from tr_ranking ON DUPLICATE KEY UPDATE id=values(id);
      COMMIT;
      DROP TABLE `tr_ranking`;

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
