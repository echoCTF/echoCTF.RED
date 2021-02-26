<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%inquiry}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m210213_145213_create_inquiry_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inquiry}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer()->unsigned()->notNull(),
            'answered'=>$this->boolean()->notNull()->defaultValue(0),
            'category' => $this->string()->defaultValue('contact'),
            'name' => $this->string(),
            'email' => $this->string(),
            'serialized' => $this->text(),
            'body'=>$this->text(),
            'updated_at'=>$this->timestamp()->notNull(),
            'created_at'=>$this->datetime(),
        ]);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-inquiry-player_id}}',
            '{{%inquiry}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-inquiry-player_id}}',
            '{{%inquiry}}',
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
            '{{%fk-inquiry-player_id}}',
            '{{%inquiry}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-inquiry-player_id}}',
            '{{%inquiry}}'
        );

        $this->dropTable('{{%inquiry}}');
    }
}
