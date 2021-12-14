<?php

use yii\db\Migration;

/**
 * Class m211214_210103_update_tai_headshot_trigger
 */
class m211214_210103_update_tai_headshot_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_headshot}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tai_headshot}} AFTER INSERT ON `headshot` FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    IF (SELECT headshot_spin FROM target WHERE id=NEW.target_id)>0 THEN
      INSERT IGNORE INTO spin_queue (target_id, player_id,created_at) VALUES (NEW.target_id,NEW.player_id,NOW());
    END IF;
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
