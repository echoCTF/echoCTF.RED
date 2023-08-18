<?php

use yii\db\Migration;

/**
 * Class m230813_215914_create_tbd_challenge_trigger
 */
class m230813_215914_create_tbd_challenge_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tbd_challenge}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tbd_challenge}} BEFORE DELETE ON {{%challenge}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
        DELETE FROM stream WHERE `model`='question' AND model_id IN (SELECT id FROM question WHERE challenge_id=OLD.ID);
        DELETE FROM team_stream WHERE `model`='question' AND model_id IN (SELECT id FROM question WHERE challenge_id=OLD.ID);
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
