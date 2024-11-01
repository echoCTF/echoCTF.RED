<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%team_invite}}`.
 */
class m241101_080235_create_team_invite_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%team_invite}}', [
      'id' => $this->primaryKey(),
      'team_id' => $this->integer(),
      'token' => $this->string(32)->notNull()->unique()->defaultValue(''),
      'created_at' => $this->dateTime(),
      'updated_at' => $this->timestamp(),
    ]);
    $this->addForeignKey('fk_team_id', 'team_invite', 'team_id', 'team', 'id', 'CASCADE', 'CASCADE');
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropForeignKey('fk_team_id', 'team_invite');
    $this->dropTable('{{%team_invite}}');
  }
}
