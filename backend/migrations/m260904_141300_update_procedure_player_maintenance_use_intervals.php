<?php

use yii\db\Migration;

class m260904_141300_update_procedure_player_maintenance_use_intervals extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%player_maintenance}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%player_maintenance}}()
BEGIN
  DECLARE player_require_approval INT;
  SET player_require_approval=memc_get('sysconfig:player_require_approval');
  SET @player_delete_inactive_after=memc_get('sysconfig:player_delete_inactive_after');
  SET @player_delete_deleted_after=memc_get('sysconfig:player_delete_deleted_after');
  SET @player_changed_to_deleted_after=memc_get('sysconfig:player_changed_to_deleted_after');
  SET @player_delete_rejected_after=memc_get('sysconfig:player_delete_rejected_after');

  IF player_require_approval IS NOT NULL AND player_require_approval>0 AND @player_delete_rejected_after IS NOT NULL THEN
    SET @sql_cmd=CONCAT('DELETE FROM `player` WHERE `status`=9 AND approval=4 AND `ts` < NOW() - INTERVAL ',@player_delete_rejected_after);
    EXECUTE IMMEDIATE @sql_cmd;
  END IF;
  IF @player_delete_inactive_after IS NOT NULL THEN
    SET @sql_cmd=CONCAT('DELETE FROM `player` WHERE `status`=9 AND `ts` < NOW() - INTERVAL ',@player_delete_inactive_after);
    EXECUTE IMMEDIATE @sql_cmd;
  END IF;
  IF @player_delete_deleted_after IS NOT NULL THEN
    SET @sql_cmd=CONCAT('DELETE FROM `player` WHERE `status`=0 AND `ts` < NOW() - INTERVAL ',@player_delete_deleted_after);
    EXECUTE IMMEDIATE @sql_cmd;
  END IF;
  IF @player_changed_to_deleted_after IS NOT NULL THEN
    SET @sql_cmd=CONCAT('UPDATE player SET status=0 WHERE status=8 AND ts < NOW() - INTERVAL ',@player_changed_to_deleted_after);
    EXECUTE IMMEDIATE @sql_cmd;
  END IF;
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