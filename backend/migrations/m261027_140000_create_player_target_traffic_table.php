<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_target_traffic}}`.
 * Per player, per target packet and byte counters filled by traffic/collect.
 * "sent" is player to target, "received" is target to player.
 */
class m261027_140000_create_player_target_traffic_table extends Migration
{
  public function safeUp()
  {
    $this->createTable('{{%player_target_traffic}}', [
      'player_id' => $this->integer(10)->unsigned()->notNull(),
      'target_id' => $this->integer(11)->notNull(),
      'packets_sent' => $this->bigInteger(20)->unsigned()->notNull()->defaultValue(0),
      'packets_received' => $this->bigInteger(20)->unsigned()->notNull()->defaultValue(0),
      'bytes_sent' => $this->bigInteger(20)->unsigned()->notNull()->defaultValue(0),
      'bytes_received' => $this->bigInteger(20)->unsigned()->notNull()->defaultValue(0),
      'first_seen' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
      'last_seen' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
    ]);

    $this->addPrimaryKey('pk-player_target_traffic', '{{%player_target_traffic}}', ['player_id', 'target_id']);
    $this->createIndex('idx-player_target_traffic-target_id', '{{%player_target_traffic}}', 'target_id');
    $this->createIndex('idx-player_target_traffic-last_seen', '{{%player_target_traffic}}', 'last_seen');

    $this->addForeignKey(
      'fk-player_target_traffic-player_id',
      '{{%player_target_traffic}}',
      'player_id',
      'player',
      'id',
      'CASCADE',
      'CASCADE'
    );
    $this->addForeignKey(
      'fk-player_target_traffic-target_id',
      '{{%player_target_traffic}}',
      'target_id',
      'target',
      'id',
      'CASCADE',
      'CASCADE'
    );
  }

  public function safeDown()
  {
    $this->dropTable('{{%player_target_traffic}}');
  }
}
