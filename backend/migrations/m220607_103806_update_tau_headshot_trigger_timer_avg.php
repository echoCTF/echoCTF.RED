<?php

use yii\db\Migration;

/**
 * Class m220607_103806_update_tau_headshot_trigger_timer_avg
 */
class m220607_103806_update_tau_headshot_trigger_timer_avg extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_headshot}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tau_headshot}} AFTER UPDATE ON {{%headshot}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    IF (OLD.rating IS NULL AND NEW.rating IS NOT NULL) OR (OLD.rating IS NOT NULL and NEW.rating!=OLD.rating) THEN
      UPDATE target_state SET player_rating=(SELECT round(avg(rating)) FROM headshot WHERE target_id=NEW.target_id AND rating>-1) WHERE id=NEW.target_id;
    END IF;
    IF (OLD.timer IS NULL AND NEW.timer IS NOT NULL) OR (OLD.timer IS NOT NULL AND NEW.timer!=OLD.timer) THEN
        UPDATE target_state SET timer_avg=(SELECT round(avg(timer)) FROM headshot WHERE target_id=NEW.target_id and timer>60) WHERE id=NEW.target_id;
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
