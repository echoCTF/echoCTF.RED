<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%network_target}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%network}}`
 * - `{{%target}}`
 */
class m191115_102820_create_junction_table_for_network_and_target_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%network_target}}', [
            'network_id' => $this->integer()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'PRIMARY KEY(network_id, target_id)',
        ]);

        // creates index for column `network_id`
        $this->createIndex(
            '{{%idx-network_target-network_id}}',
            '{{%network_target}}',
            'network_id'
        );

        // add foreign key for table `{{%network}}`
        $this->addForeignKey(
            '{{%fk-network_target-network_id}}',
            '{{%network_target}}',
            'network_id',
            '{{%network}}',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-network_target-target_id}}',
            '{{%network_target}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-network_target-target_id}}',
            '{{%network_target}}',
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
        // drops foreign key for table `{{%network}}`
        $this->dropForeignKey(
            '{{%fk-network_target-network_id}}',
            '{{%network_target}}'
        );

        // drops index for column `network_id`
        $this->dropIndex(
            '{{%idx-network_target-network_id}}',
            '{{%network_target}}'
        );

        // drops foreign key for table `{{%target}}`
        $this->dropForeignKey(
            '{{%fk-network_target-target_id}}',
            '{{%network_target}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-network_target-target_id}}',
            '{{%network_target}}'
        );

        $this->dropTable('{{%network_target}}');
    }
}
