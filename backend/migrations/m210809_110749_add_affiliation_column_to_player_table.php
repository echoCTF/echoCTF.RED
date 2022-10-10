<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%player}}`.
 */
class m210809_110749_add_affiliation_column_to_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('player', 'affiliation', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('player', 'affiliation');
    }
}
