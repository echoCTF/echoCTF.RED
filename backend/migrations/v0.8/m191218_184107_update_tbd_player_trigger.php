<?php

use yii\db\Migration;

/**
 * Class m191218_184107_update_tad_player_trigger
 */
class m191218_184107_update_tbd_player_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbd_player}}";
  public function up()
  {

    $CREATE_SQL="CREATE TRIGGER {{%tbd_player}} BEFORE DELETE ON {{%player}} FOR EACH ROW
    BEGIN
      DELETE FROM player_ssl WHERE player_id=OLD.id;
      DELETE FROM player_rank WHERE player_id=OLD.id;
    END";
      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }

}
