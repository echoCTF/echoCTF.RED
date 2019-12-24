<?php

use yii\db\Migration;

/**
 * Class m191105_074351_add_player_score_points_index
 */
class m191105_074351_add_player_score_points_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createIndex(
          'idx-player_score-points',
          '{{%player_score}}',
          'points'
      );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropIndex(
        'idx-player_score-points',
        '{{%player_score}}'
      );
    }
}
