<?php

use yii\db\Migration;

/**
 * Class m220515_004033_create_tau_headshot_trigger
 */
class m220515_004033_create_tau_headshot_trigger extends Migration
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
