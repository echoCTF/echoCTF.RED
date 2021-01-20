<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%target_ondemand}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%target}}`
 * - `{{%player}}`
 */
class m210119_125422_create_target_ondemand_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%target_ondemand}}', [
            'target_id' => $this->integer()->notNull(),
            'player_id' => $this->integer()->unsigned(),
            'state' => $this->tinyInteger()->notNull()->defaultValue(-1),
            'heartbeat' => $this->datetime(),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
        ]);
        $this->addPrimaryKey ( 'pidPK', '{{%target_ondemand}}', 'target_id' );
        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-target_ondemand-target_id}}',
            '{{%target_ondemand}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-target_ondemand-target_id}}',
            '{{%target_ondemand}}',
            'target_id',
            '{{%target}}',
            'id',
            'CASCADE'
        );

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-target_ondemand-player_id}}',
            '{{%target_ondemand}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-target_ondemand-player_id}}',
            '{{%target_ondemand}}',
            'player_id',
            '{{%player}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%target}}`
        $this->dropForeignKey(
            '{{%fk-target_ondemand-target_id}}',
            '{{%target_ondemand}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-target_ondemand-target_id}}',
            '{{%target_ondemand}}'
        );

        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-target_ondemand-player_id}}',
            '{{%target_ondemand}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-target_ondemand-player_id}}',
            '{{%target_ondemand}}'
        );

        $this->dropTable('{{%target_ondemand}}');
    }
}
