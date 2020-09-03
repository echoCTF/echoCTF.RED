<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%challenge_solver}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%challenge}}`
 * - `{{%player}}`
 */
class m200903_172014_create_challenge_solver_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%challenge_solver}}', [
            'challenge_id' => $this->integer()->notNull(),
            'player_id' => $this->integer()->notNull()->unsigned(),
            'timer' => $this->bigInteger(),
            'rating' => $this->integer(),
            'created_at' => $this->datetime(),
        ]);
        $this->addPrimaryKey('challenge_solver_pk', '{{%challenge_solver}}', ['challenge_id', 'player_id']);

        // creates index for column `challenge_id`
        $this->createIndex(
            '{{%idx-challenge_solver-challenge_id}}',
            '{{%challenge_solver}}',
            'challenge_id'
        );

        // add foreign key for table `{{%challenge}}`
        $this->addForeignKey(
            '{{%fk-challenge_solver-challenge_id}}',
            '{{%challenge_solver}}',
            'challenge_id',
            '{{%challenge}}',
            'id',
            'CASCADE'
        );

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-challenge_solver-player_id}}',
            '{{%challenge_solver}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-challenge_solver-player_id}}',
            '{{%challenge_solver}}',
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
        // drops foreign key for table `{{%challenge}}`
        $this->dropForeignKey(
            '{{%fk-challenge_solver-challenge_id}}',
            '{{%challenge_solver}}'
        );

        // drops index for column `challenge_id`
        $this->dropIndex(
            '{{%idx-challenge_solver-challenge_id}}',
            '{{%challenge_solver}}'
        );

        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-challenge_solver-player_id}}',
            '{{%challenge_solver}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-challenge_solver-player_id}}',
            '{{%challenge_solver}}'
        );

        $this->dropTable('{{%challenge_solver}}');
    }
}
