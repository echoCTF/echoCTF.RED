<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_spin}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m191007_085714_create_player_spin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_spin}}', [
            'player_id' => $this->integer()->unsigned()->notNull(),
            'counter' => $this->integer(),
            'total' => $this->integer(),
            'updated_at' => $this->date(),
        ]);
        $this->addPrimaryKey('played_spin_pk','{{%player_spin}}',['player_id']);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-player_spin-player_id}}',
            '{{%player_spin}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-player_spin-player_id}}',
            '{{%player_spin}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );
        $this->db->createCommand("INSERT INTO {{%player_spin}} (player_id,counter,total,updated_at) SELECT {{%id}},0,0,now() FROM {{%player}}")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-player_spin-player_id}}',
            '{{%player_spin}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-player_spin-player_id}}',
            '{{%player_spin}}'
        );

        $this->dropTable('{{%player_spin}}');
    }
}
