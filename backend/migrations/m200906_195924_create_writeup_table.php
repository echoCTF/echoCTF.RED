<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%writeup}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 * - `{{%target}}`
 */
class m200906_195924_create_writeup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%writeup}}', [
            'player_id' => $this->integer()->unsigned()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'content' => 'LONGBLOB',
            'approved' => $this->boolean()->defaultValue(0),
            'status' => "ENUM('PENDING','NEEDS FIXES','REJECTED','OK')",
            'comment' => 'LONGBLOB',
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
        $this->addPrimaryKey('{{%PK-writeup}}', '{{%writeup}}', ['player_id', 'target_id']);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-writeup-player_id}}',
            '{{%writeup}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-writeup-player_id}}',
            '{{%writeup}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            '{{%idx-writeup-target_id}}',
            '{{%writeup}}',
            'target_id'
        );

        // add foreign key for table `{{%target}}`
        $this->addForeignKey(
            '{{%fk-writeup-target_id}}',
            '{{%writeup}}',
            'target_id',
            '{{%target}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-writeup-player_id}}',
            '{{%writeup}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-writeup-player_id}}',
            '{{%writeup}}'
        );

        // drops foreign key for table `{{%target}}`
        $this->dropForeignKey(
            '{{%fk-writeup-target_id}}',
            '{{%writeup}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-writeup-target_id}}',
            '{{%writeup}}'
        );

        $this->dropTable('{{%writeup}}');
    }
}
