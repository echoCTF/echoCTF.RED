<?php

use yii\db\Migration;

/**
 * Class m220514_224351_create_tai_treasure_trigger
 */
class m220514_224351_create_tai_treasure_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_treasure}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tai_treasure}} AFTER INSERT ON {{%treasure}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET total_treasures=total_treasures+1,total_points=total_points+ifnull(NEW.points,0),treasure_points=treasure_points+ifnull(NEW.points,0) WHERE id=NEW.target_id;
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
