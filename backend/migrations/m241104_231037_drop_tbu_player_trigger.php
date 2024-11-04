<?php

use yii\db\Migration;

/**
 * Class m241104_231037_drop_tbu_player_trigger
 */
class m241104_231037_drop_tbu_player_trigger extends Migration
{
  public $DROP_SQL = "DROP TRIGGER IF EXISTS {{%tbu_player}}";

  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }

  public function down()
  {
    echo "Nothing to reverse...";
  }
}
