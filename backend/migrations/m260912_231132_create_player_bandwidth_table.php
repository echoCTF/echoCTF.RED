<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_bandwidth}}`.
 */
class m260912_231132_create_player_bandwidth_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%player_bandwidth}}', [
      'id' => $this->primaryKey(),
      'player_id' => $this->integer(10)->unsigned()->notNull(),
      'vpn_local_address' => $this->integer(10)->unsigned()->defaultValue(null),
      'bytes_received' => $this->bigInteger(20)->unsigned()->notNull()->defaultValue(0),
      'bytes_sent' => $this->bigInteger(20)->unsigned()->notNull()->defaultValue(0),
      'duration' => $this->integer(10)->unsigned()->notNull()->defaultValue(0),
      'ts' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
    ]);

    $this->createIndex(
      'idx-player_bandwidth-player_id',
      '{{%player_bandwidth}}',
      'player_id'
    );

    $this->createIndex(
      'idx-player_bandwidth-ts',
      '{{%player_bandwidth}}',
      'ts'
    );

    $this->addForeignKey(
      'fk-player_bandwidth-player_id',
      '{{%player_bandwidth}}',
      'player_id',
      'player',
      'id',
      'CASCADE',
      'CASCADE'
    );
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('{{%player_bandwidth}}');
  }
}
