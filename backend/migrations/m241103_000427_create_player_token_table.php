<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_token}}`.
 */
class m241103_000427_create_player_token_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%player_token}}', [
      'player_id' => $this->integer()->unsigned()->notNull(),
      'type' => $this->string(32)->notNull()->defaultValue('API'),
      'token' => $this->string(128)->notNull()->unique(),
      'description' => $this->text()->notNull()->defaultValue(""),
      'expires_at' => $this->dateTime(),
      'created_at' => $this->timestamp(),
    ]);
    $this->addPrimaryKey('player_token-pk', 'player_token', ['player_id', 'type']);
    $this->addForeignKey('fk-player_token-player_id-player', '{{%player_token}}', 'player_id', 'player', 'id', 'CASCADE', 'CASCADE');
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%player_token}}');
  }
}
