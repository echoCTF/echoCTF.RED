<?php

use yii\db\Migration;

/**
 * Class m241027_110150_update_calculate_team_ranks_procedure_to_make_dynamic
 */
class m241027_110150_update_calculate_team_ranks_procedure_to_make_dynamic extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%calculate_team_ranks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%calculate_team_ranks}} ()
  BEGIN
  DECLARE v_max INT unsigned DEFAULT 0;
  DECLARE v_counter INT unsigned DEFAULT 0;

  SET v_max=(SELECT IFNULL(memc_get('sysconfig:academic_grouping'),0));

  DROP TABLE IF EXISTS `tr_ranking`;

  IF v_max = 0 THEN
    CREATE TEMPORARY TABLE `tr_ranking` (id int primary key AUTO_INCREMENT,team_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    START TRANSACTION;
    delete from team_rank;
    insert into tr_ranking select NULL,t.team_id from team_score as t left join team as t2 on t.team_id=t2.id ORDER BY points desc,t.ts asc, t.team_id asc;
    insert IGNORE into team_rank select * from tr_ranking;
    COMMIT;
    DROP TABLE `tr_ranking`;
  ELSE
    IF (SELECT count(*) FROM sysconfig WHERE id='teams')>0 AND (SELECT val FROM sysconfig WHERE id='teams')=1 THEN
      REPEAT
        CREATE TEMPORARY TABLE `tr_ranking` (id int primary key AUTO_INCREMENT,team_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        START TRANSACTION;
          -- DELETE team of a given academic category or no longer exist in the table
          delete from team_rank where team_id in (select id from team where academic=v_counter) OR team_id NOT IN (select id from team);
          insert into tr_ranking select NULL,t.team_id from team_score as t left join team as t2 on t.team_id=t2.id WHERE t2.academic=v_counter order by points desc,t.ts asc, t.team_id asc;
          insert IGNORE into team_rank select * from tr_ranking;
        COMMIT;
        DROP TABLE `tr_ranking`;
        SET v_counter=v_counter+1;
        UNTIL  v_counter >= v_max
      END REPEAT;
    END IF;
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
