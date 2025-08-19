<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%abuser}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m250818_191856_create_abuser_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%abuser}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer()->unsigned()->notNull(),
            'title' => $this->string(),
            'body' => $this->text(),
            'reason' => $this->string(),
            'model' => $this->string()->notNull(),
            'model_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-abuser-player_id}}',
            '{{%abuser}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-abuser-player_id}}',
            '{{%abuser}}',
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
            '{{%fk-abuser-player_id}}',
            '{{%abuser}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-abuser-player_id}}',
            '{{%abuser}}'
        );

        $this->dropTable('{{%abuser}}');
    }
}
