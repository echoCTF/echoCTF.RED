<?php

use yii\db\Migration;

/**
 * Class m241027_110007_update_calculate_ranks_procedure_to_make_dynamic
 */
class m241027_110007_update_calculate_ranks_procedure_to_make_dynamic extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%calculate_ranks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%calculate_ranks}} ()
  BEGIN
  DECLARE v_max INT unsigned DEFAULT 0;
  DECLARE v_counter INT unsigned DEFAULT 0;

  SET v_max=(SELECT IFNULL(memc_get('sysconfig:academic_grouping'),0));
  DROP TABLE IF EXISTS pr_ranking;
  REPEAT
    CREATE TEMPORARY TABLE `pr_ranking` (id int primary key AUTO_INCREMENT,player_id int) ENGINE=MEMORY CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    START TRANSACTION;
      delete from player_rank where player_id in (select id from player where academic=v_counter) OR player_id NOT IN (select id from player);
      insert into pr_ranking select NULL,t.player_id from player_score as t left join player as t2 on t.player_id=t2.id where t2.active=1 and t2.status=10 and t2.academic=v_counter order by points desc,t.ts asc, t.player_id asc;
      insert IGNORE into player_rank select * from pr_ranking;
    COMMIT;
    DROP TABLE `pr_ranking`;
    SET v_counter=v_counter+1;
    UNTIL  v_counter < v_max
  END REPEAT;
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
