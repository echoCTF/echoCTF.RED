<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%network}}`.
 */
class m210204_125000_add_weight_column_to_network_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%network}}', 'weight', $this->integer()->notNull()->defaultValue(0));
      $this->createIndex(
          '{{%idx-network-weight}}',
          '{{%network}}',
          'weight'
      );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('{{%network}}', 'weight');
    }
}
