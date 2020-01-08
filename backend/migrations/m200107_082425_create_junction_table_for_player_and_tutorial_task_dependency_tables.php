<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_tutorial_task}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 * - `{{%tutorial_task_dependency}}`
 */
class m200107_082425_create_junction_table_for_player_and_tutorial_task_dependency_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_tutorial_task}}', [
            'player_id' => $this->integer()->unSigned()->notNull(),
            'tutorial_task_dependency_id' => $this->integer(),
            'points' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'PRIMARY KEY(player_id, tutorial_task_dependency_id)',
        ]);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-player_tutorial_task-player_id}}',
            '{{%player_tutorial_task}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-player_tutorial_task-player_id}}',
            '{{%player_tutorial_task}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );

        // creates index for column `tutorial_task_dependency_id`
        $this->createIndex(
            '{{%idx-player_tutorial_task-tutorial_task_dependency_id}}',
            '{{%player_tutorial_task}}',
            'tutorial_task_dependency_id'
        );

        // add foreign key for table `{{%tutorial_task_dependency}}`
        $this->addForeignKey(
            '{{%fk-player_tutorial_task-tutorial_task_dependency_id}}',
            '{{%player_tutorial_task}}',
            'tutorial_task_dependency_id',
            '{{%tutorial_task_dependency}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-player_tutorial_task-player_id}}',
            '{{%player_tutorial_task}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-player_tutorial_task-player_id}}',
            '{{%player_tutorial_task}}'
        );

        // drops foreign key for table `{{%tutorial_task_dependency}}`
        $this->dropForeignKey(
            '{{%fk-player_tutorial_task-tutorial_task_dependency_id}}',
            '{{%player_tutorial_task}}'
        );

        // drops index for column `tutorial_task_dependency_id`
        $this->dropIndex(
            '{{%idx-player_tutorial_task-tutorial_task_dependency_id}}',
            '{{%player_tutorial_task}}'
        );

        $this->dropTable('{{%player_tutorial_task}}');
    }
}
