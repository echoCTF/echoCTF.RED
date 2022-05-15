<?php

use yii\db\Migration;

/**
 * Class m220514_230211_update_tad_headshot_trigger_add_target_state
 */
class m220514_230211_update_tad_headshot_trigger_add_target_state extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_headshot}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_headshot}} AFTER DELETE ON {{%headshot}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET total_headshots=total_headshots-1,timer_avg=(SELECT round(avg(timer)) FROM headshot WHERE target_id=OLD.target_id) WHERE id=OLD.target_id;
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
