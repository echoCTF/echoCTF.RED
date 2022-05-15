<?php

use yii\db\Migration;

/**
 * Class m220514_225608_update_tai_finding_trigger_add_target_state
 */
class m220514_225608_update_tai_finding_trigger_add_target_state extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_finding}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tai_finding}} AFTER INSERT ON {{%finding}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    DO memc_set(CONCAT('finding:',NEW.protocol,':',ifnull(NEW.port,0), ':', NEW.target_id ),NEW.id);
    UPDATE target_state SET total_findings=total_findings+1,total_points=total_points+IFNULL(NEW.points,0),finding_points=finding_points+IFNULL(NEW.points,0) WHERE id=NEW.target_id;
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
