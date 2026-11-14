<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_streak}}` and procedure `add_player_streak`.
 * Streaks are counted in weeks (monday to sunday) with at least one claim.
 */
class m261114_143000_create_player_streak_table extends Migration
{
  public $DROP_SQL = "DROP PROCEDURE IF EXISTS {{%add_player_streak}}";
  public $CREATE_SQL = "CREATE PROCEDURE {{%add_player_streak}} (IN usid INT UNSIGNED, IN claim_ts DATETIME)
  BEGIN
    DECLARE wk DATE DEFAULT DATE(claim_ts) - INTERVAL WEEKDAY(claim_ts) DAY;
    INSERT INTO player_streak (player_id,current,best,last_week) VALUES (usid,1,1,wk)
      ON DUPLICATE KEY UPDATE
        current=IF(last_week>=wk, current, IF(last_week=wk - INTERVAL 7 DAY, current+1, 1)),
        best=GREATEST(best,current),
        last_week=GREATEST(last_week,wk);
  END";

  public function safeUp()
  {
    $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
    $this->createTable('{{%player_streak}}', [
      'player_id' => $this->integer()->unsigned()->notNull(),
      'current' => $this->integer()->unsigned()->notNull()->defaultValue(0),
      'best' => $this->integer()->unsigned()->notNull()->defaultValue(0),
      'last_week' => $this->date(),
    ], $tableOptions);
    $this->addPrimaryKey('pk-player_streak', '{{%player_streak}}', ['player_id']);
    $this->createIndex('idx-player_streak-current', '{{%player_streak}}', ['current']);
    $this->addForeignKey('fk-player_streak-player_id', '{{%player_streak}}', 'player_id', '{{%player}}', 'id', 'CASCADE');

    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();

    // backfill from existing claims
    $weeks = $this->db->createCommand("SELECT player_id, DATE(ts) - INTERVAL WEEKDAY(ts) DAY AS wk FROM (SELECT player_id,ts FROM player_finding UNION ALL SELECT player_id,ts FROM player_treasure) AS claims GROUP BY player_id,wk ORDER BY player_id,wk")->queryAll();
    $rows = [];
    foreach ($weeks as $w) {
      $pid = $w['player_id'];
      if (!isset($rows[$pid])) {
        $rows[$pid] = [$pid, 1, 1, $w['wk']];
        continue;
      }
      $r = &$rows[$pid];
      $r[1] = (strtotime($r[3] . ' +7 days') === strtotime($w['wk'])) ? $r[1] + 1 : 1;
      $r[2] = max($r[2], $r[1]);
      $r[3] = $w['wk'];
      unset($r);
    }
    foreach (array_chunk(array_values($rows), 1000) as $chunk) {
      $this->batchInsert('{{%player_streak}}', ['player_id', 'current', 'best', 'last_week'], $chunk);
    }
  }

  public function safeDown()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->dropTable('{{%player_streak}}');
  }
}
