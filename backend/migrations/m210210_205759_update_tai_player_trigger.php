<?php

use yii\db\Migration;

/**
 * Class m210210_205759_update_tai_player_trigger
 */
class m210210_205759_update_tai_player_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_player}}";
  public $CREATE_SQL="CREATE TRIGGER tai_player AFTER INSERT ON player FOR EACH ROW
  thisBegin:BEGIN
    DECLARE ltitle VARCHAR(20) DEFAULT 'Joined the platform';

    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;

    IF (select memc_server_count()<1) THEN
      select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    SELECT memc_set(CONCAT('player_type:',NEW.id), NEW.type) INTO @devnull;
    SELECT memc_set(CONCAT('player:',NEW.id), NEW.id) INTO @devnull;
    INSERT INTO profile (player_id) VALUES (NEW.id);
    INSERT INTO player_last (id,on_pui) VALUES (NEW.id,now());
    INSERT INTO player_spin (player_id,counter,total,perday,updated_at) values (NEW.id,0,0,memc_get('sysconfig:spins_per_day'),NOW());
    INSERT INTO player_score (player_id) VALUES (NEW.id);
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
