<?php

use yii\db\Migration;

class m250914_084533_update_trigger_after_update_on_player_last_table extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player_last}}";
  public $CREATE_SQL="CREATE TRIGGER {{%tau_player_last}} AFTER UPDATE ON {{%player_last}} FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
  IF NOT (OLD.vpn_local_address <=> NEW.vpn_local_address) THEN
    IF NEW.vpn_local_address IS NOT NULL THEN
    -- Set key if ip is not null
      SET @local_ip  = INET_NTOA(NEW.vpn_local_address);
      SET @remote_ip = INET_NTOA(NEW.vpn_remote_address);
      DO memc_set(CONCAT('ovpn:', NEW.id), @local_ip);
      DO memc_set(CONCAT('ovpn:', @local_ip), NEW.id);
      DO memc_set(CONCAT('ovpn_remote:', NEW.id), @remote_ip);
    ELSE
    -- Delete the keys if not
      SET @old_ip = INET_NTOA(OLD.vpn_local_address);
      DO memc_delete(CONCAT('ovpn:', NEW.id));
      DO memc_delete(CONCAT('ovpn_remote:', NEW.id));
      DO memc_delete(CONCAT('ovpn:', @old_ip));
    END IF;
  END IF;

  IF NOT (OLD.vpn_local_address <=> NEW.vpn_local_address)  THEN
    INSERT INTO `player_vpn_history` (`player_id`,`vpn_local_address`,`vpn_remote_address`) VALUES (NEW.id,NEW.vpn_local_address,NEW.vpn_remote_address);
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