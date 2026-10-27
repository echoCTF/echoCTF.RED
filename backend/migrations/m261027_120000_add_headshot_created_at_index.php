<?php

use yii\db\Migration;

/**
 * Adds an index on headshot.created_at so "latest headshot" lookups
 * (ORDER BY created_at DESC LIMIT N) avoid a full table scan and filesort.
 */
class m261027_120000_add_headshot_created_at_index extends Migration
{
  public function safeUp()
  {
    $this->createIndex('idx-headshot-created_at', '{{%headshot}}', 'created_at');
  }

  public function safeDown()
  {
    $this->dropIndex('idx-headshot-created_at', '{{%headshot}}');
  }
}
