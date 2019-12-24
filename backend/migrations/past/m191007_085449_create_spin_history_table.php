<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%spin_history}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%target}}`
 * - `{{%player}}`
 */
class m191007_085449_create_spin_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%spin_history}}', [
            'id' => $this->primaryKey(),
            'target_id' => $this->integer()->notNull(),
            'player_id' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-spin_history-target_id}}',
            '{{%spin_history}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-spin_history-target_id}}',
            '{{%spin_history}}',
            'target_id',
            '{{%target}}',
            'id',
            'CASCADE'
        );

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-spin_history-player_id}}',
            '{{%spin_history}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-spin_history-player_id}}',
            '{{%spin_history}}',
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
        $this->dropTable('{{%spin_history}}');
    }
}
