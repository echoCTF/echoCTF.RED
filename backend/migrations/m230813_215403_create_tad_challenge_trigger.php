<?php

use yii\db\Migration;

/**
 * Class m230813_215403_create_tad_challenge_trigger
 */
class m230813_215403_create_tad_challenge_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_challenge}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_challenge}} AFTER DELETE ON {{%challenge}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
        DELETE FROM stream WHERE `model`='challenge' AND model_id=OLD.id;
        DELETE FROM team_stream WHERE `model`='challenge' AND model_id=OLD.id;
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
