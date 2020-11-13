<?php

use yii\db\Migration;

/**
 * Class m201109_221657_create_tad_team_trigger
 */
class m201109_221657_create_tad_team_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_team}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tad_team}} AFTER DELETE ON {{%team}} FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    DELETE FROM {{%team_score}} WHERE team_id=OLD.id;
    DELETE FROM {{%team_rank}} WHERE team_id=OLD.id;
    DELETE FROM {{%team_stream}} WHERE team_id=OLD.id;
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
