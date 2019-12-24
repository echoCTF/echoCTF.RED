<?php

use yii\db\Migration;

/**
 * Class m191015_082107_create_tau_finding_trigger
 */
class m191015_082107_create_tau_finding_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_finding}}";

    public function up()
    {
      $CREATE_SQL="CREATE TRIGGER {{%tau_finding}} AFTER UPDATE ON {{%finding}} FOR EACH ROW
BEGIN
  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;

  IF NEW.protocol != OLD.protocol OR NEW.port!=OLD.port OR NEW.target_id!=OLD.target_id THEN
    SELECT memc_delete(CONCAT('finding:',OLD.protocol,':',ifnull(OLD.port,0), ':', OLD.target_id )) INTO @devnull;
  END IF;
  SELECT memc_set(CONCAT('finding:',NEW.protocol,':',ifnull(NEW.port,0), ':', NEW.target_id ),NEW.id) INTO @devnull;
END";

      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($CREATE_SQL)->execute();

    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      return true;
    }
}
