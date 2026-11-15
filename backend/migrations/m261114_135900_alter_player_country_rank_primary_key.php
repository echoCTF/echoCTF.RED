<?php

use yii\db\Migration;

class m261114_135900_alter_player_country_rank_primary_key extends Migration
{
  public function up()
  {
    // ranks repeat per academic group, so country is no longer unique per rank
    $this->db->createCommand("ALTER TABLE {{%player_country_rank}} DROP PRIMARY KEY, ADD PRIMARY KEY (`id`,`player_id`) USING BTREE")->execute();
  }

  public function down()
  {
    // memory table rebuilt by calculate_country_rank, empty it so the old key fits
    $this->db->createCommand("DELETE FROM {{%player_country_rank}}")->execute();
    $this->db->createCommand("ALTER TABLE {{%player_country_rank}} DROP PRIMARY KEY, ADD PRIMARY KEY (`id`,`country`) USING BTREE")->execute();
  }
}
