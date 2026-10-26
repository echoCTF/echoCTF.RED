<?php

use yii\db\Migration;

class m261026_130000_alter_player_metadata_affiliation_length extends Migration
{
  public function safeUp()
  {
    $this->alterColumn('player_metadata', 'affiliation', $this->string(255)->null()->defaultValue(null));
  }

  public function safeDown()
  {
    $this->alterColumn('player_metadata', 'affiliation', $this->string(64)->null()->defaultValue(null));
  }
}
