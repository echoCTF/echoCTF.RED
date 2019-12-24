<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%network_player}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%network}}`
 * - `{{%player}}`
 */
class m191115_112849_create_junction_table_for_network_and_player_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%network_player}}', [
            'network_id' => $this->integer()->notNull(),
            'player_id' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'PRIMARY KEY(network_id, player_id)',
        ]);

        // creates index for column `network_id`
        $this->createIndex(
            '{{%idx-network_player-network_id}}',
            '{{%network_player}}',
            'network_id'
        );

        // add foreign key for table `{{%network}}`
        $this->addForeignKey(
            '{{%fk-network_player-network_id}}',
            '{{%network_player}}',
            'network_id',
            '{{%network}}',
            'id',
            'CASCADE'
        );

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-network_player-player_id}}',
            '{{%network_player}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-network_player-player_id}}',
            '{{%network_player}}',
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
        // drops foreign key for table `{{%network}}`
        $this->dropForeignKey(
            '{{%fk-network_player-network_id}}',
            '{{%network_player}}'
        );

        // drops index for column `network_id`
        $this->dropIndex(
            '{{%idx-network_player-network_id}}',
            '{{%network_player}}'
        );

        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-network_player-player_id}}',
            '{{%network_player}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-network_player-player_id}}',
            '{{%network_player}}'
        );

        $this->dropTable('{{%network_player}}');
    }
}
