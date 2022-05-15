<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%target_states}}`.
 */
class m220514_211321_create_target_state_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%target_state}}', [
            'id' => $this->integer()->notNull(),
            'total_headshots'=>$this->integer()->unsigned()->notNull()->defaultValue(0),
            'total_findings'=>$this->integer()->unsigned()->notNull()->defaultValue(0),
            'total_treasures'=>$this->integer()->unsigned()->notNull()->defaultValue(0),
            'player_rating'=>$this->integer()->notNull()->defaultValue(-1),
            'timer_avg'=>$this->integer()->unsigned()->notNull()->defaultValue(0),
            'total_writeups'=>$this->integer()->unsigned()->notNull()->defaultValue(0),
            'approved_writeups'=>$this->integer()->unsigned()->notNull()->defaultValue(0),
            'finding_points'=>$this->integer()->unsigned()->notNull()->defaultValue(0),
            'treasure_points'=>$this->integer()->unsigned()->notNull()->defaultValue(0),
            'total_points'=>$this->integer()->unsigned()->notNull()->defaultValue(0),
            'on_network'=>$this->smallInteger()->unsigned()->notNull()->defaultValue(0),
            'on_ondemand'=>$this->smallInteger()->unsigned()->notNull()->defaultValue(0),
            'ondemand_state'=>$this->smallInteger()->notNull()->defaultValue(-1),
            'PRIMARY KEY (id)',
        ]);

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-target_states-id}}',
            '{{%target_state}}',
            'id',
            '{{%target}}',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-target_state-total_headshots',
            'target_state',
            'total_headshots'
        );
        $this->createIndex(
            'idx-target_state-total_findings',
            'target_state',
            'total_findings'
        );
        $this->createIndex(
            'idx-target_state-total_treasures',
            'target_state',
            'total_treasures'
        );
        $this->createIndex(
            'idx-target_state-player_rating',
            'target_state',
            'player_rating'
        );
        $this->createIndex(
            'idx-target_state-timer_avg',
            'target_state',
            'timer_avg'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%target_state}}');
    }
}
