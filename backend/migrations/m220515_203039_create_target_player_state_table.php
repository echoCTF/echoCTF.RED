<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%target_player_state}}`.
 */
class m220515_203039_create_target_player_state_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%target_player_state}}', [
            'id' => $this->integer()->notNull(),
            'player_id' => $this->integer()->unsigned()->notNull(),
            'player_treasures' => $this->integer()->notNull()->defaultValue(0),
            'player_findings'=> $this->integer()->notNull()->defaultValue(0),
            'player_points'=> $this->integer()->notNull()->defaultValue(0),
            'PRIMARY KEY (id,player_id)',
        ]);

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-target_player_state-id}}',
            '{{%target_player_state}}',
            'id',
            '{{%target}}',
            'id',
            'CASCADE'
        );
        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-target_player_state-player_id}}',
            '{{%target_player_state}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-target_player_state-player_treasures',
            'target_player_state',
            'player_treasures'
        );
        $this->createIndex(
            'idx-target_player_state-player_findings',
            'target_player_state',
            'player_findings'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%target_player_state}}');
    }
}
