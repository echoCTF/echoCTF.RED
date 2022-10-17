<?php

use yii\db\Migration;

/**
 * Class m210803_203701_alter_player_rank_primary_key
 */
class m210803_203701_alter_player_rank_primary_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->dropPrimaryKey('', 'player_rank', ['id']);
      $this->addPrimaryKey('', 'player_rank', ['id', 'player_id']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropPrimaryKey('', 'player_rank', ['id', 'player_id']);
      $this->addPrimaryKey('', 'player_rank', ['id']);
    }
}
