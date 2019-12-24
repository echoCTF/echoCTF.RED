<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%spin_queue}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%target}}`
 * - `{{%player}}`
 */
class m191007_085337_create_spin_queue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%spin_queue}}', [
            'target_id' => $this->integer()->notNull(),
            'player_id' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->dateTime(),
        ]);
        $this->addPrimaryKey('spin_queue_pk','{{%spin_queue}}',['target_id']);
        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-spin_queue-target_id}}',
            '{{%spin_queue}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-spin_queue-target_id}}',
            '{{%spin_queue}}',
            'target_id',
            '{{%target}}',
            'id',
            'CASCADE'
        );

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-spin_queue-player_id}}',
            '{{%spin_queue}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-spin_queue-player_id}}',
            '{{%spin_queue}}',
            'player_id',
            '{{%player}}',
            'id',
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
            '{{%fk-spin_queue-target_id}}',
            '{{%spin_queue}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-spin_queue-target_id}}',
            '{{%spin_queue}}'
        );

        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-spin_queue-player_id}}',
            '{{%spin_queue}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-spin_queue-player_id}}',
            '{{%spin_queue}}'
        );

        $this->dropTable('{{%spin_queue}}');
    }
}
