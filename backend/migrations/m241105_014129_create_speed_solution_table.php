<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%speed_solution}}`.
 */
class m241105_014129_create_speed_solution_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%speed_solution}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer()->unsigned()->notNull(),
            'problem_id' => $this->integer()->notNull(),
            'language' => $this->string(),
            'sourcecode' => 'LONGBLOB',
            'status' => $this->string(),
            'points' => $this->integer()->defaultValue(0),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
        ]);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-speed_solution-player_id}}',
            '{{%speed_solution}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-speed_solution-player_id}}',
            '{{%speed_solution}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );

        // creates index for column `problem_id`
        $this->createIndex(
          '{{%idx-speed_solution-problem_id}}',
          '{{%speed_solution}}',
          'problem_id'
      );

      // add foreign key for table `{{%speed_problem}}`
      $this->addForeignKey(
          '{{%fk-speed_solution-problem_id}}',
          '{{%speed_solution}}',
          'problem_id',
          '{{%speed_problem}}',
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
            '{{%fk-speed_solution-player_id}}',
            '{{%speed_solution}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-speed_solution-player_id}}',
            '{{%speed_solution}}'
        );

        // drops foreign key for table `{{%team}}`
        $this->dropForeignKey(
            '{{%fk-speed_solution-team_id}}',
            '{{%speed_solution}}'
        );

        // drops index for column `team_id`
        $this->dropIndex(
            '{{%idx-speed_solution-team_id}}',
            '{{%speed_solution}}'
        );

        // drops foreign key for table `{{%target}}`
        $this->dropForeignKey(
            '{{%fk-speed_solution-target_id}}',
            '{{%speed_solution}}'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            '{{%idx-speed_solution-target_id}}',
            '{{%speed_solution}}'
        );

        $this->dropTable('{{%speed_solution}}');
    }
}
