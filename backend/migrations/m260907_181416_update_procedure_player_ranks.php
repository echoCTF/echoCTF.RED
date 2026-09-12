<?php

use yii\db\Migration;

class m260907_181416_update_procedure_player_ranks extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%player_ranks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%player_ranks}}()
BEGIN
  DROP TABLE IF EXISTS player_rank_new;
  CREATE TABLE player_rank_new (
    `id` int(11) unsigned NOT NULL DEFAULT 0,
    `player_id` int(11) NOT NULL,
    PRIMARY KEY (`id`,`player_id`) USING BTREE,
    UNIQUE KEY `player_id` (`player_id`) USING BTREE
  ) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  INSERT INTO player_rank_new (id, player_id)
  SELECT ROW_NUMBER() OVER (PARTITION BY t2.academic ORDER BY t.points DESC, t.ts ASC, t.player_id ASC), t.player_id
  FROM player_score t
  JOIN player t2 ON t.player_id=t2.id
  WHERE t2.active=1 AND t2.status=10;

  RENAME TABLE player_rank TO player_rank_old, player_rank_new TO player_rank;
  DROP TABLE player_rank_old;
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