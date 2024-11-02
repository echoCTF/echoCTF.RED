<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_disconnect_queue}}`.
 */
class m241102_094821_create_player_disconnect_queue_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%player_disconnect_queue}}', [
      'player_id' => 'int(10) unsigned NOT NULL',
      'created_at' => $this->timestamp(),
    ]);
    $this->addPrimaryKey('pk_on_player_id', '{{%player_disconnect_queue}}', 'player_id');
    $this->addForeignKey('fk-player_id-player', '{{%player_disconnect_queue}}', 'player_id', 'player', 'id', 'CASCADE','CASCADE');
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropForeignKey('fk-player_id-player', '{{%player_disconnect_queue}}');
    $this->dropTable('{{%player_disconnect_queue}}');
  }
}
