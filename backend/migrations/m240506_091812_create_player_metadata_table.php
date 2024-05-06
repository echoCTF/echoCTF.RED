<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_metadata}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m240506_091812_create_player_metadata_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_metadata}}', [
            'player_id' => $this->primaryKey()->unsigned()->notNull(),
            'identificationFile'=>$this->string(64),
            'affiliation'=>$this->string(64),
        ]);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-player_metadata-player_id}}',
            '{{%player_metadata}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-player_metadata-player_id}}',
            '{{%player_metadata}}',
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
            '{{%fk-player_metadata-player_id}}',
            '{{%player_metadata}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-player_metadata-player_id}}',
            '{{%player_metadata}}'
        );

        $this->dropTable('{{%player_metadata}}');
    }
}
