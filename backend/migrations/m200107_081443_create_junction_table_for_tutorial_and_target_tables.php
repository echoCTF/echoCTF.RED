<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tutorial_target}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%tutorial}}`
 * - `{{%target}}`
 */
class m200107_081443_create_junction_table_for_tutorial_and_target_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tutorial_target}}', [
            'tutorial_id' => $this->integer(),
            'target_id' => $this->integer(),
            'weight' => $this->smallInteger(),
            'PRIMARY KEY(tutorial_id, target_id)',
        ]);

        // creates index for column `tutorial_id`
        $this->createIndex(
            '{{%idx-tutorial_target-tutorial_id}}',
            '{{%tutorial_target}}',
            'tutorial_id'
        );

        // add foreign key for table `{{%tutorial}}`
        $this->addForeignKey(
            '{{%fk-tutorial_target-tutorial_id}}',
            '{{%tutorial_target}}',
            'tutorial_id',
            '{{%tutorial}}',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-tutorial_target-target_id}}',
            '{{%tutorial_target}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-tutorial_target-target_id}}',
            '{{%tutorial_target}}',
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
        // drops foreign key for table `{{%tutorial}}`
        $this->dropForeignKey(
            '{{%fk-tutorial_target-tutorial_id}}',
            '{{%tutorial_target}}'
        );

        // drops index for column `tutorial_id`
        $this->dropIndex(
            '{{%idx-tutorial_target-tutorial_id}}',
            '{{%tutorial_target}}'
        );

        // drops foreign key for table `{{%target}}`
        $this->dropForeignKey(
            '{{%fk-tutorial_target-target_id}}',
            '{{%tutorial_target}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-tutorial_target-target_id}}',
            '{{%tutorial_target}}'
        );

        $this->dropTable('{{%tutorial_target}}');
    }
}
