<?php

use yii\db\Migration;

/**
 * Class m220515_124225_create_tai_network_target_trigger
 */
class m220515_124225_create_tai_network_target_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_network_target}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tai_network_target}} AFTER INSERT ON {{%network_target}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET on_network=1 WHERE id=NEW.target_id and on_network!=1;
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
