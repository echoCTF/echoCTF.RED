<?php

use yii\db\Migration;

class m260907_123414_alter_rank_tables_remove_indexes extends Migration
{
  private $indexes = [
    'player_rank' => 'idx-player_rank-player_id',
    'team_rank' => 'idx-team_rank-team_id'
  ];

  public function safeUp()
  {
    foreach ($this->indexes as $table => $idx)
      if ($this->indexExists($table, $idx)) {
        $this->dropIndex($idx, $table);
      }
  }

  public function safeDown()
  {
    return true;
  }

  private function indexExists($table, $idx)
  {
    foreach ($this->db->schema->getTableIndexes($table) as $index) {
      if ($index->name === $idx) {
        return true;
      }
    }
    return false;
  }
}
