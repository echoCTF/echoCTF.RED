<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%private_network}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m251125_074851_create_private_network_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function up()
  {
    if ($this->db->schema->getTableSchema('{{%private_network}}', true) !== null) {
      $this->dropTable('{{%private_network}}');
    }

    $this->createTable('{{%private_network}}', [
      'id' => $this->primaryKey(),
      'player_id' => $this->integer()->unsigned(),
      'name' => $this->string(),
      'team_accessible' => $this->boolean(),
      'created_at' => $this->dateTime(),
    ]);

    // creates index for column `player_id`
    $this->createIndex(
      '{{%idx-private_network-player_id}}',
      '{{%private_network}}',
      'player_id'
    );

    // add foreign key for table `{{%player}}`
    $this->addForeignKey(
      '{{%fk-private_network-player_id}}',
      '{{%private_network}}',
      'player_id',
      '{{%player}}',
      'id',
      'SET NULL',
      'CASCADE'
    );
  }

  /**
   * {@inheritdoc}
   */
  public function down()
  {
    if ($this->db->schema->getTableSchema('{{%private_network}}', true) !== null)
      $this->dropTable('{{%private_network}}');
  }
}
