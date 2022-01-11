<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%writeup_rating}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%writeup}}`
 * - `{{%player}}`
 */
class m220110_220014_create_writeup_rating_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%writeup_rating}}', [
            'id' => $this->primaryKey(),
            'writeup_id' => $this->integer()->notNull(),
            'player_id' => $this->integer(10)->unsigned()->notNull(),
            'rating' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'unique key (writeup_id,player_id)'
        ]);

        // creates index for column `id`
        $this->createIndex(
            '{{%idx-writeup_rating-writeup_id}}',
            '{{%writeup_rating}}',
            'writeup_id'
        );

        // add foreign key for table `{{%writeup}}`
        $this->addForeignKey(
            '{{%fk-writeup_rating-writeup_id}}',
            '{{%writeup_rating}}',
            'writeup_id',
            '{{%writeup}}',
            'id',
            'CASCADE'
        );

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-writeup_rating-player_id}}',
            '{{%writeup_rating}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-writeup_rating-player_id}}',
            '{{%writeup_rating}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // drops foreign key for table `{{%writeup}}`
        $this->dropForeignKey(
            '{{%fk-writeup_rating-writeup_id}}',
            '{{%writeup_rating}}'
        );

        // drops index for column `id`
        $this->dropIndex(
            '{{%idx-writeup_rating-writeup_id}}',
            '{{%writeup_rating}}'
        );

        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-writeup_rating-player_id}}',
            '{{%writeup_rating}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-writeup_rating-player_id}}',
            '{{%writeup_rating}}'
        );

        $this->dropTable('{{%writeup_rating}}');
    }
}
