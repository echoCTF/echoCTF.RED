<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%private_network_target}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%private_network}}`
 * - `{{%target}}`
 */
class m251125_095103_create_private_network_target_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%private_network_target}}', [
      'id' => $this->primaryKey(),
      'private_network_id' => $this->integer(),
      'target_id' => $this->integer(),
      'ip' => $this->integer()->unsigned(),
      'state' => $this->smallInteger()->unsigned()->defaultValue(0),
      'server_id' => $this->integer(),
      'ipoctet' => "VARCHAR(15) GENERATED ALWAYS AS (INET_NTOA(ip)) VIRTUAL",
    ]);

    // creates unique index on network/target combo
    $this->createIndex(
      '{{%idx-unique-private_network_id-target_id}}',
      '{{%private_network_target}}',
      ['private_network_id', 'target_id'],
      true
    );

    // creates index for column `private_network_id`
    $this->createIndex(
      '{{%idx-private_network_target-private_network_id}}',
      '{{%private_network_target}}',
      'private_network_id'
    );

    // creates index for column `server_id`
    $this->createIndex(
      '{{%idx-private_network_target-server_id}}',
      '{{%private_network_target}}',
      'server_id'
    );

    // add foreign key for table `{{%private_network}}`
    $this->addForeignKey(
      '{{%fk-private_network_target-private_network_id}}',
      '{{%private_network_target}}',
      'private_network_id',
      '{{%private_network}}',
      'id',
      'CASCADE'
    );

    // add foreign key for table `{{%server}}`
    $this->addForeignKey(
      '{{%fk-private_network_target-server_id}}',
      '{{%private_network_target}}',
      'server_id',
      '{{%server}}',
      'id',
      'SET NULL',
      'CASCADE'
    );

    // creates index for column `target_id`
    $this->createIndex(
      '{{%idx-private_network_target-target_id}}',
      '{{%private_network_target}}',
      'target_id'
    );

    // add foreign key for table `{{%target}}`
    $this->addForeignKey(
      '{{%fk-private_network_target-target_id}}',
      '{{%private_network_target}}',
      'target_id',
      '{{%target}}',
      'id',
      'CASCADE'
    );
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    if ($this->db->schema->getTableSchema('{{%private_network_target}}', true) !== null)
      $this->dropTable('{{%private_network_target}}');
  }
}
