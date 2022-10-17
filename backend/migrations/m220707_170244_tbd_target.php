<?php

use yii\db\Migration;

/**
 * Class m220707_170244_tbd_target
 */
class m220707_170244_tbd_target extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbd_target}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tbd_target}} BEFORE DELETE ON {{%target}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
        DELETE FROM headshot WHERE target_id=OLD.id;
        DELETE FROM finding WHERE target_id=OLD.id;
        DELETE FROM treasure WHERE target_id=OLD.id;
        DELETE FROM target_state WHERE id=OLD.id;
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
