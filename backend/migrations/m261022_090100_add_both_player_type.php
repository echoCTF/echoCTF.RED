<?php

use yii\db\Migration;

class m261022_090100_add_both_player_type extends Migration
{
  private $tables = ['achievement', 'challenge', 'credential', 'question', 'treasure'];

  public function safeUp()
  {
    foreach ($this->tables as $table) {
      $this->alterColumn('{{%' . $table . '}}', 'player_type', "ENUM('offense','defense','both') NOT NULL DEFAULT 'both'");
    }
  }

  public function safeDown()
  {
    foreach ($this->tables as $table) {
      $this->update('{{%' . $table . '}}', ['player_type' => 'offense'], ['player_type' => 'both']);
      $this->alterColumn('{{%' . $table . '}}', 'player_type', "ENUM('offense','defense') NOT NULL DEFAULT 'offense'");
    }
  }
}
