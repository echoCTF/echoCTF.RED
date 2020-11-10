<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%team_stream}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%team}}`
 * - `{{%stream}}`
 */
class m201109_225939_create_junction_table_for_team_and_stream_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%team_stream}}', [
            'team_id' => $this->integer()->notNull(),
            'model'=>$this->string(255)->append('CHARACTER SET utf8 COLLATE utf8_bin'),
            'model_id'=>$this->integer()->defaultValue(null),
            'points'=>$this->float()->notNull()->defaultValue(0),
            'ts'=>$this->timestamp(),
            'PRIMARY KEY(team_id, model, model_id)',
        ]);

        $this->createIndex(
            '{{%idx-team_stream-team_id}}',
            '{{%team_stream}}',
            'team_id'
        );

        $this->createIndex(
            '{{%idx-team_stream-model}}',
            '{{%team_stream}}',
            'model'
        );

        $this->createIndex(
            '{{%idx-team_stream-model_id}}',
            '{{%team_stream}}',
            'model_id'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `team_id`
        $this->dropIndex(
            '{{%idx-team_stream-team_id}}',
            '{{%team_stream}}'
        );
        $this->dropIndex(
            '{{%idx-team_stream-model}}',
            '{{%team_stream}}'
        );

        $this->dropIndex(
            '{{%idx-team_stream-model_id}}',
            '{{%team_stream}}'
        );

        $this->dropTable('{{%team_stream}}');
    }
}
