<?php

use yii\db\Migration;

/**
 * Class m200903_195125_add_unique_index_on_player_question_table
 */
class m200903_195125_add_unique_index_on_player_question_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      // creates index for column `challenge_id`
      $this->createIndex(
          '{{%uidx-player_question-player_id-question_id}}',
          '{{%player_question}}',
          'question_id,player_id',
          1
      );


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropIndex('{{%uidx-player_question-player_id-question_id}}');
    }

}
