<?php

use yii\db\Migration;

/**
 * Adds `cost` to player_target_help.
 *
 * cost=0 marks a legacy (free) activation that keeps the 50% halving on
 * remaining treasures and findings. cost>0 marks a paid activation, the
 * points were charged up front and remaining claims award full points.
 * Existing rows get cost=0 so they keep the halving.
 */
class m261026_150000_add_cost_column_to_player_target_help_table extends Migration
{
  public function safeUp()
  {
    $this->addColumn('{{%player_target_help}}', 'cost', $this->integer()->unsigned()->notNull()->defaultValue(0)->after('target_id'));
  }

  public function safeDown()
  {
    $this->dropColumn('{{%player_target_help}}', 'cost');
  }
}
