<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%headshot}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 * - `{{%target}}`
 */
class m191217_141104_create_headshot_junction_table_for_player_and_target_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%headshot}}', [
            'player_id' => $this->integer(10)->unsigned()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'PRIMARY KEY(player_id, target_id)',
        ]);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-headshot-player_id}}',
            '{{%headshot}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-headshot-player_id}}',
            '{{%headshot}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-headshot-target_id}}',
            '{{%headshot}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-headshot-target_id}}',
            '{{%headshot}}',
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
            '{{%fk-headshot-player_id}}',
            '{{%headshot}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-headshot-player_id}}',
            '{{%headshot}}'
        );

        // drops foreign key for table `{{%target}}`
        $this->dropForeignKey(
            '{{%fk-headshot-target_id}}',
            '{{%headshot}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-headshot-target_id}}',
            '{{%headshot}}'
        );

        $this->dropTable('{{%headshot}}');
    }
}
