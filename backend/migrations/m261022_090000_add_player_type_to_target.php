<?php

use yii\db\Migration;

class m261022_090000_add_player_type_to_target extends Migration
{
  public function safeUp()
  {
    $this->addColumn('{{%target}}', 'player_type', "ENUM('offense','defense','both') NOT NULL DEFAULT 'both'");
  }

  public function safeDown()
  {
    $this->dropColumn('{{%target}}', 'player_type');
  }
}
