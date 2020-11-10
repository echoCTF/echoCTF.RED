<?php

use yii\db\Migration;

/**
 * Class m201110_013115_drop_update_player_ranks_event
 */
class m201110_013115_drop_update_player_ranks_event extends Migration
{
  public $DROP_SQL="DROP EVENT IF EXISTS {{%update_player_ranks}}";
    public function up()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
    }
}
