<?php

use yii\db\Migration;

/**
 * Class m220707_161655_tad_treasure_fix
 */
class m220707_161655_tad_treasure_fix extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_treasure}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_treasure}} AFTER DELETE ON {{%treasure}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET total_treasures=total_treasures-1,total_points=total_points-IFNULL(OLD.points,0),treasure_points=treasure_points-IFNULL(OLD.points,0) WHERE id=OLD.target_id;
    DELETE FROM stream WHERE model_id=OLD.id and model='treasure';
    DELETE FROM team_stream WHERE model_id=OLD.id and model='treasure';
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
