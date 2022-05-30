<?php

use yii\db\Migration;

/**
 * Class m220530_213836_update_tai_headshot_trigger_schedule_ondemand
 */
class m220530_213836_update_tai_headshot_trigger_schedule_ondemand extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_headshot}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tai_headshot}} AFTER INSERT ON {{%headshot}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    IF (SELECT headshot_spin FROM target WHERE id=NEW.target_id)>0 THEN
      INSERT IGNORE INTO spin_queue (target_id, player_id,created_at) VALUES (NEW.target_id,NEW.player_id,NOW());
    END IF;
    IF (SELECT count(*) FROM target_ondemand WHERE target_id=NEW.target_id AND state=1)>0 THEN
        UPDATE target_ondemand SET heartbeat=(NOW() - INTERVAL 59 MINUTE - INTERVAL 30 SECOND) WHERE target_id=NEW.target_id;
    END IF;
    UPDATE target_state SET total_headshots=total_headshots+1,timer_avg=(SELECT round(avg(timer)) FROM headshot WHERE target_id=NEW.target_id) WHERE id=NEW.target_id;
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
