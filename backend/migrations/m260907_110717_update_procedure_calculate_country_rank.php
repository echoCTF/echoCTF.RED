<?php

use yii\db\Migration;

class m260907_110717_update_procedure_calculate_country_rank extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%calculate_country_rank}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%calculate_country_rank}}()
BEGIN
  IF (SELECT val FROM sysconfig WHERE id='country_rankings') = 1 THEN
    DROP TABLE IF EXISTS player_country_rank_new;
    CREATE TABLE player_country_rank_new (
      `id` int(11) NOT NULL DEFAULT 0,
      `player_id` int(11) unsigned NOT NULL,
      `country` varchar(3) NOT NULL,
      PRIMARY KEY (`id`,`country`)  USING BTREE,
      UNIQUE KEY `idx-player_country_rank-player_id` (`player_id`) USING BTREE
    ) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO player_country_rank_new (id, player_id, country)
    SELECT ROW_NUMBER() OVER (PARTITION BY COALESCE(t3.country, 'UNK') ORDER BY t.points DESC, t.ts ASC, t.player_id ASC),
           t3.player_id, COALESCE(t3.country, 'UNK')
    FROM player_score t
    JOIN player t2 ON t.player_id = t2.id
    JOIN profile t3 ON t.player_id = t3.player_id
    WHERE t2.active = 1 AND t2.status = 10;

    RENAME TABLE player_country_rank TO player_country_rank_old, player_country_rank_new TO player_country_rank;
    DROP TABLE player_country_rank_old;
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