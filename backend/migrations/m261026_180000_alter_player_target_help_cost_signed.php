<?php

use yii\db\Migration;

/**
 * Makes player_target_help.cost a signed integer. With UNSIGNED, the
 * -1*NEW.cost expression in tai_player_target_help fails with
 * "BIGINT UNSIGNED value is out of range".
 */
class m261026_180000_alter_player_target_help_cost_signed extends Migration
{
  public function safeUp()
  {
    $this->alterColumn('{{%player_target_help}}', 'cost', $this->integer()->notNull()->defaultValue(0));
  }

  public function safeDown()
  {
    $this->alterColumn('{{%player_target_help}}', 'cost', $this->integer()->unsigned()->notNull()->defaultValue(0));
  }
}
