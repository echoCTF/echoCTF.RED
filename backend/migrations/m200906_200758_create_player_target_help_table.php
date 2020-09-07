<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_target_help}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 * - `{{%target}}`
 */
class m200906_200758_create_player_target_help_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_target_help}}', [
            'player_id' => $this->integer()->unsigned()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
        ]);
        $this->addPrimaryKey('{{%PK-player_target_help}}', '{{%player_target_help}}', ['player_id', 'target_id']);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-player_target_help-player_id}}',
            '{{%player_target_help}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-player_target_help-player_id}}',
            '{{%player_target_help}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-player_target_help-target_id}}',
            '{{%player_target_help}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-player_target_help-target_id}}',
            '{{%player_target_help}}',
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
        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-player_target_help-player_id}}',
            '{{%player_target_help}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-player_target_help-player_id}}',
            '{{%player_target_help}}'
        );

        // drops foreign key for table `{{%target}}`
        $this->dropForeignKey(
            '{{%fk-player_target_help-target_id}}',
            '{{%player_target_help}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-player_target_help-target_id}}',
            '{{%player_target_help}}'
        );

        $this->dropTable('{{%player_target_help}}');
    }
}
