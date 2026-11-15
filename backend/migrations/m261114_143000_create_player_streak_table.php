<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_streak}}`.
 * Streaks are counted in weeks (monday to sunday) with at least one claim.
 */
class m261114_143000_create_player_streak_table extends Migration
{
  public function safeUp()
  {
    $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
    $this->createTable('{{%player_streak}}', [
      'player_id' => $this->integer()->unsigned()->notNull(),
      'current' => $this->integer()->unsigned()->notNull()->defaultValue(0),
      'best' => $this->integer()->unsigned()->notNull()->defaultValue(0),
      'last_week' => $this->date(),
    ], $tableOptions);
    $this->addPrimaryKey('pk-player_streak', '{{%player_streak}}', ['player_id']);
    $this->createIndex('idx-player_streak-current', '{{%player_streak}}', ['current']);
    $this->addForeignKey('fk-player_streak-player_id', '{{%player_streak}}', 'player_id', '{{%player}}', 'id', 'CASCADE');
  }

  public function safeDown()
  {
    $this->dropTable('{{%player_streak}}');
  }
}
