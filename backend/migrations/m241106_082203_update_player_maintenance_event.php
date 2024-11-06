<?php

use yii\db\Migration;

/**
 * Class m241106_082203_update_player_maintenance_event
 */
class m241106_082203_update_player_maintenance_event extends Migration
{
  public $DROP_SQL = "DROP EVENT IF EXISTS {{%player_maintenance}}";
  public $CREATE_SQL = "CREATE EVENT {{%player_maintenance}} ON SCHEDULE EVERY 1 DAY STARTS '2020-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO
  BEGIN
    CALL player_maintenance();
  END";

  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }
}
