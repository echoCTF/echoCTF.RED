<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_relation}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m211207_164930_create_player_relation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_relation}}', [
            'player_id' => $this->integer()->unsigned(),
            'referred_id' => $this->integer()->unsigned(),
            'PRIMARY KEY (player_id)',
        ]);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-player_relation-player_id}}',
            '{{%player_relation}}',
            'player_id'
        );
        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-player_relation-referred_id}}',
            '{{%player_relation}}',
            'referred_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-player_relation-player_id}}',
            '{{%player_relation}}',
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
        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-player_relation-player_id}}',
            '{{%player_relation}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-player_relation-player_id}}',
            '{{%player_relation}}'
        );

        $this->dropTable('{{%player_relation}}');
    }
}
