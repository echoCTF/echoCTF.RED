<?php

use yii\db\Migration;

/**
 * Class m220515_003413_create_tau_writeup_trigger
 */
class m220515_003413_create_tau_writeup_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_writeup}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tau_writeup}} AFTER UPDATE ON {{%writeup}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;

    IF NEW.approved=1 and OLD.approved=0 THEN
       UPDATE target_state SET approved_writeups=approved_writeups+1 WHERE id=NEW.target_id;
    ELSEIF NEW.approved=0 and OLD.approved=1 THEN
       UPDATE target_state SET approved_writeups=approved_writeups-1 WHERE id=NEW.target_id;
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
