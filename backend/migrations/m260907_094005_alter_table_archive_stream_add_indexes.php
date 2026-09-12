<?php

use yii\db\Migration;

/**
 * Adds player_id index to archived_stream, skips if already present.
 */
class m260907_094005_alter_table_archive_stream_add_indexes extends Migration
{
  private $table = 'archived_stream';
  private $index = 'idx-archived_stream-player_id';

  public function safeUp()
  {
    if ($this->db->schema->getTableSchema($this->table) === null) {
      return true;
    }

    if (!$this->indexExists()) {
      $this->createIndex($this->index, $this->table, 'player_id');
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
