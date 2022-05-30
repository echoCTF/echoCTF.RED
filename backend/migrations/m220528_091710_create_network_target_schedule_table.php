<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%network_target_schedule}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%target}}`
 * - `{{%network}}`
 */
class m220528_091710_create_network_target_schedule_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%network_target_schedule}}', [
            'id' => $this->primaryKey(),
            'target_id' => $this->integer()->notNull(),
            'network_id' => $this->integer(),
            'migration_date' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);

        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-network_target_schedule-target_id}}',
            '{{%network_target_schedule}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-network_target_schedule-target_id}}',
            '{{%network_target_schedule}}',
            'target_id',
            '{{%target}}',
            'id',
            'CASCADE'
        );

        // creates index for column `network_id`
        $this->createIndex(
            '{{%idx-network_target_schedule-network_id}}',
            '{{%network_target_schedule}}',
            'network_id'
        );

        // add foreign key for table `{{%network}}`
        $this->addForeignKey(
            '{{%fk-network_target_schedule-network_id}}',
            '{{%network_target_schedule}}',
            'network_id',
            '{{%network}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%target}}`
        $this->dropForeignKey(
            '{{%fk-network_target_schedule-target_id}}',
            '{{%network_target_schedule}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-network_target_schedule-target_id}}',
            '{{%network_target_schedule}}'
        );

        // drops foreign key for table `{{%network}}`
        $this->dropForeignKey(
            '{{%fk-network_target_schedule-network_id}}',
            '{{%network_target_schedule}}'
        );

        // drops index for column `network_id`
        $this->dropIndex(
            '{{%idx-network_target_schedule-network_id}}',
            '{{%network_target_schedule}}'
        );

        $this->dropTable('{{%network_target_schedule}}');
    }
}
