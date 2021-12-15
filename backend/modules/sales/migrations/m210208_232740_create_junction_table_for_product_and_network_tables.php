<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_network}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%product}}`
 * - `{{%network}}`
 */
class m210208_232740_create_junction_table_for_product_and_network_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_network}}', [
            'product_id' => $this->string(40)->notNull(),
            'network_id' => $this->integer(),
            'PRIMARY KEY(product_id, network_id)',
        ]);

        // creates index for column `product_id`
        $this->createIndex(
            '{{%idx-product_network-product_id}}',
            '{{%product_network}}',
            'product_id'
        );

        // add foreign key for table `{{%product}}`
        $this->addForeignKey(
            '{{%fk-product_network-product_id}}',
            '{{%product_network}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE'
        );

        // creates index for column `network_id`
        $this->createIndex(
            '{{%idx-product_network-network_id}}',
            '{{%product_network}}',
            'network_id'
        );

        // add foreign key for table `{{%network}}`
        $this->addForeignKey(
            '{{%fk-product_network-network_id}}',
            '{{%product_network}}',
            'network_id',
            '{{%network}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%product}}`
        $this->dropForeignKey(
            '{{%fk-product_network-product_id}}',
            '{{%product_network}}'
        );

        // drops index for column `product_id`
        $this->dropIndex(
            '{{%idx-product_network-product_id}}',
            '{{%product_network}}'
        );

        // drops foreign key for table `{{%network}}`
        $this->dropForeignKey(
            '{{%fk-product_network-network_id}}',
            '{{%product_network}}'
        );

        // drops index for column `network_id`
        $this->dropIndex(
            '{{%idx-product_network-network_id}}',
            '{{%product_network}}'
        );

        $this->dropTable('{{%product_network}}');
    }
}
