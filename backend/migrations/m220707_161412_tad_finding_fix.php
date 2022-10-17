<?php

use yii\db\Migration;

/**
 * Class m220707_161412_tad_finding_fix
 */
class m220707_161412_tad_finding_fix extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tad_finding}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tad_finding}} AFTER DELETE ON {{%finding}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    DO memc_delete(CONCAT('finding:',OLD.protocol,':',ifnull(OLD.port,0), ':', OLD.target_id ));

    UPDATE target_state SET total_findings=total_findings-1,total_points=total_points-IFNULL(OLD.points,0),finding_points=finding_points-IFNULL(OLD.points,0) WHERE id=OLD.target_id;
    DELETE FROM stream WHERE model_id=OLD.id AND model='finding';
    DELETE FROM team_stream WHERE model_id=OLD.id and model='finding';
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
