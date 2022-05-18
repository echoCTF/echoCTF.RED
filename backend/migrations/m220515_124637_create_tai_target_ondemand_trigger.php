<?php

use yii\db\Migration;

/**
 * Class m220515_124637_create_tai_target_ondemand_trigger
 */
class m220515_124637_create_tai_target_ondemand_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_target_ondemand}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tai_target_ondemand}} AFTER INSERT ON {{%target_ondemand}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET on_ondemand=1,ondemand_state=NEW.state WHERE id=NEW.target_id;
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
