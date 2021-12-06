<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_disabledroute}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m211206_215320_create_player_disabledroute_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_disabledroute}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer()->unsigned(),
            'route' => $this->string(),
        ]);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-player_disabledroute-player_id}}',
            '{{%player_disabledroute}}',
            'player_id'
        );
        $this->createIndex(
            '{{%idx-player_disabledroute-route}}',
            '{{%player_disabledroute}}',
            'route'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-player_disabledroute-player_id}}',
            '{{%player_disabledroute}}',
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
            '{{%fk-player_disabledroute-player_id}}',
            '{{%player_disabledroute}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-player_disabledroute-player_id}}',
            '{{%player_disabledroute}}'
        );

        $this->dropTable('{{%player_disabledroute}}');
    }
}
