<?php

use yii\db\Migration;

/**
 * Class m241102_114127_create_tai_player_disconnect_queue_trigger
 */
class m241102_114127_create_tai_player_disconnect_queue_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_player_disconnect_queue}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_player_disconnect_queue}} AFTER INSERT ON {{%player_disconnect_queue}} FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
    INSERT INTO player_disconnect_queue_history (player_id,created_at) VALUES (NEW.player_id,NOW());
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
