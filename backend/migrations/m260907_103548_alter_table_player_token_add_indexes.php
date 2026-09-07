<?php

use yii\db\Migration;

class m260907_103548_alter_table_player_token_add_indexes extends Migration
{
  private $table = 'player_token';
  private $index = 'idx-player_token-expires_at';

  public function safeUp()
  {
    if (!$this->indexExists()) {
      $this->createIndex($this->index, $this->table, 'expires_at');
    }
  }

  public function safeDown()
  {
    if ($this->indexExists()) {
      $this->dropIndex($this->index, $this->table);
    }
  }

  private function indexExists()
  {
    foreach ($this->db->schema->getTableIndexes($this->table) as $index) {
      if ($index->name === $this->index) {
        return true;
      }
    }
    return false;
  }
}
