<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_token_history}}`.
 */
class m241103_105725_create_player_token_history_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%player_token_history}}', [
      'id'=>$this->primaryKey(),
      'player_id' => $this->integer()->unsigned()->notNull(),
      'type' => $this->string(32)->notNull()->defaultValue('API'),
      'token' => $this->string(128)->notNull(),
      'description' => $this->text()->notNull()->defaultValue(''),
      'expires_at' => $this->dateTime(),
      'created_at' => $this->timestamp(),
      'ts' => $this->timestamp(),
    ]);
    $this->addForeignKey('fk-player_token_history-player_id-player', '{{%player_token_history}}', 'player_id', 'player', 'id', 'CASCADE', 'CASCADE');
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%player_token}}');
  }
}
