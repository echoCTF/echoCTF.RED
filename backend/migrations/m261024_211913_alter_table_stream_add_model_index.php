<?php

use yii\db\Migration;

class m261024_211913_alter_table_stream_add_model_index extends Migration
{
  private $table = 'stream';
  private $index = 'idx-stream-model-model_id';

  public function safeUp()
  {
    if (!$this->indexExists()) {
      $this->createIndex($this->index, $this->table, ['model(20)', 'model_id']);
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
