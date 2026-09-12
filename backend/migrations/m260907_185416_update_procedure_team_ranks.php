<?php

use yii\db\Migration;

class m260907_185416_update_procedure_team_ranks extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%team_ranks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%team_ranks}}()
BEGIN
  IF (SELECT val FROM sysconfig WHERE id='teams') = 1 THEN
    DROP TABLE IF EXISTS team_rank_new;
    CREATE TABLE team_rank_new (
      `id` int(11) NOT NULL DEFAULT 0,
      `team_id` int(11) NOT NULL,
      PRIMARY KEY (`id`,`team_id`) USING BTREE,
      UNIQUE KEY `team_id` (`team_id`) USING BTREE
    ) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO team_rank_new (id, team_id)
    SELECT ROW_NUMBER() OVER (PARTITION BY t2.academic ORDER BY t.points DESC, t.ts ASC, t.team_id ASC), t.team_id
    FROM team_score t
    JOIN team t2 ON t.team_id=t2.id;

    RENAME TABLE team_rank TO team_rank_old, team_rank_new TO team_rank;
    DROP TABLE team_rank_old;
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