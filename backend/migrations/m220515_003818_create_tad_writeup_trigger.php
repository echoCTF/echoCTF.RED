<?php

use yii\db\Migration;

/**
 * Class m220515_003818_create_tad_writeup_trigger
 */
class m220515_003818_create_tad_writeup_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_writeup}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_writeup}} AFTER DELETE ON {{%writeup}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;

    IF OLD.approved=1 THEN
       UPDATE target_state SET approved_writeups=approved_writeups-1,total_writeups=total_writeups-1 WHERE id=OLD.target_id;
    ELSEIF OLD.approved=0 THEN
       UPDATE target_state SET total_writeups=total_writeups-1 WHERE id=OLD.target_id;
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
