<?php

use yii\db\Migration;

/**
 * Class m220511_213708_update_tau_player_last_trigger_with_memcache
 */
class m220511_213708_update_tau_player_last_trigger_with_memcache extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player_last}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tau_player_last}} AFTER UPDATE ON {{%player_last}} FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;

    IF (OLD.vpn_local_address IS NULL AND NEW.vpn_local_address IS NOT NULL) THEN
        DO memc_set(CONCAT('ovpn:',NEW.id),INET_NTOA(NEW.vpn_local_address));
        DO memc_set(CONCAT('ovpn:',INET_NTOA(NEW.vpn_local_address)),NEW.id);
        DO memc_set(CONCAT('ovpn_remote:',NEW.id),INET_NTOA(NEW.vpn_remote_address));
    ELSEIF (OLD.vpn_local_address IS NOT NULL AND NEW.vpn_local_address IS NULL) THEN
        DO memc_delete(CONCAT('ovpn:',NEW.id));
        DO memc_delete(CONCAT('ovpn_remote:',NEW.id));
        DO memc_delete(CONCAT('ovpn:',INET_NTOA(OLD.vpn_local_address)));
    END IF;

    IF (OLD.vpn_local_address IS NULL AND NEW.vpn_local_address IS NOT NULL) OR (OLD.vpn_local_address IS NOT NULL AND NEW.vpn_local_address IS NOT NULL AND NEW.vpn_local_address!=OLD.vpn_local_address) THEN
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
