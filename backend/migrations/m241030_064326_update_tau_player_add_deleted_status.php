<?php

use yii\db\Migration;

/**
 * Class m241030_064326_update_tau_player_add_deleted_status
 */
class m241030_064326_update_tau_player_add_deleted_status extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tau_player}} AFTER UPDATE ON {{%player}} FOR EACH ROW
  thisBegin:BEGIN
  DECLARE ltitle VARCHAR(30) DEFAULT \"Joined the platform\";
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;

  IF NEW.type!=OLD.type THEN
    SELECT memc_set(CONCAT('player_type:',NEW.id), NEW.type) INTO @devnull;
  END IF;

  IF NEW.status=0 AND OLD.status=10 THEN
    INSERT INTO archived_stream SELECT * FROM stream WHERE player_id=NEW.id;
    DELETE FROM stream WHERE player_id=NEW.id;
  ELSEIF NEW.status=10 AND OLD.status=0 THEN
    INSERT INTO stream SELECT * FROM archived_stream WHERE player_id=NEW.id;
    DELETE FROM archived_stream WHERE player_id=NEW.id;
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
