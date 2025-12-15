<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ws_token}}`.
 */
class m251214_115824_create_ws_token_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%ws_token}}', [
      'token' => 'VARBINARY(32) PRIMARY KEY',
      'player_id' => $this->integer(10)->unsigned()->defaultValue(null),
      'subject_id' => $this->binary(32)->notNull(),
      'is_server' => $this->boolean()->notNull()->defaultValue(0),
      'expires_at' => $this->dateTime()->notNull(),
    ]);

    $this->createIndex(
      'idx-ws_token-player_id',
      'ws_token',
      'player_id'
    );

    // add foreign key for table `user`
    $this->addForeignKey(
      'fk-ws_token-player_id',
      'ws_token',
      'player_id',
      'player',
      'id',
      'CASCADE',
      'CASCADE'
    );

    $this->createIndex(
      'idx-ws_token-server_expires',
      '{{%ws_token}}',
      ['is_server', 'expires_at']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%ws_token}}');
  }
}
