<?php

use yii\db\Migration;

class m261114_143050_refill_player_streaks extends Migration
{
  public function up()
  {
    $this->db->createCommand("CALL {{%refill_player_streaks}}()")->execute();
  }

  public function down()
  {
    $this->db->createCommand("DELETE FROM {{%player_streak}}")->execute();
  }
}
