<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_disconnect_queue_history}}`.
 */
class m241102_113836_create_player_disconnect_queue_history_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%player_disconnect_queue_history}}', [
      'id' => $this->primaryKey(),
      'player_id' => $this->integer()->notNull(),
      'created_at' => $this->timestamp(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%player_disconnect_queue_history}}');
  }
}
