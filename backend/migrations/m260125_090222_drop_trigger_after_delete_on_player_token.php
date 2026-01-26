<?php

use yii\db\Migration;

class m260125_090222_drop_trigger_after_delete_on_player_token extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_player_token}}";


  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }
}