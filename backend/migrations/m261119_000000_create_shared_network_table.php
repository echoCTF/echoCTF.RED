<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shared_network}}`.
 * Stores IPv4 ranges of shared networks (universities, NAT pools, etc.)
 * that produce legitimate duplicate login IPs and can be excluded
 * from duplicate-IP checks.
 */
class m261119_000000_create_shared_network_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('{{%shared_network}}', [
      'id' => $this->primaryKey(),
      'network' => $this->integer()->unsigned()->notNull(),
      'mask' => $this->integer()->unsigned()->notNull(),
      'description' => $this->string(),
      'created_at' => $this->dateTime(),
      'updated_at' => $this->dateTime(),
    ]);

    // Prevents exact duplicates, the containment checks are done at model level
    $this->createIndex(
      '{{%idx-shared_network-network_mask}}',
      '{{%shared_network}}',
      ['network', 'mask'],
      true
    );
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropIndex('{{%idx-shared_network-network_mask}}', '{{%shared_network}}');
    $this->dropTable('{{%shared_network}}');
  }
}
