<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%archived_stream}}`.
 */
class m241030_064325_create_archived_stream_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->db->createCommand("CREATE TABLE IF NOT EXISTS archived_stream AS SELECT * FROM stream WHERE id is null")->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%archived_stream}}');
  }
}
