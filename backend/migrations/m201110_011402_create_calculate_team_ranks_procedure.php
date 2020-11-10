<?php

use yii\db\Migration;

/**
 * Class m201110_011402_create_calculate_team_ranks_procedure
 */
class m201110_011402_create_calculate_team_ranks_procedure extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%calculate_team_ranks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%calculate_team_ranks}} ()
  BEGIN
    IF (SELECT count(*) FROM sysconfig WHERE id='teams')>0 AND (SELECT val FROM sysconfig WHERE id='teams')=1 THEN
      CREATE TEMPORARY TABLE `ranking` (id int primary key AUTO_INCREMENT,team_id int) ENGINE=MEMORY;
      START TRANSACTION;
        insert into ranking select NULL,t.team_id from team_score as t left join team as t2 on t.team_id=t2.id order by points desc,t.ts asc, t.team_id asc;
        insert into team_rank select * from ranking ON DUPLICATE KEY UPDATE id=values(id);
      COMMIT;
      DROP TABLE `ranking`;
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
