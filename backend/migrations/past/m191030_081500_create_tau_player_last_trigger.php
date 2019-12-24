<?php

use yii\db\Migration;

/**
 * Class m191030_081500_create_tau_player_last_trigger
 */
class m191030_081500_create_tau_player_last_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player_last}}";
    public function up()
    {
      $CREATE_SQL="CREATE TRIGGER {{%tau_player_last}} AFTER UPDATE ON {{%player_last}} FOR EACH ROW
BEGIN
  IF (OLD.vpn_local_address IS NULL AND NEW.vpn_local_address IS NOT NULL) OR (OLD.vpn_local_address IS NOT NULL AND NEW.vpn_local_address IS NOT NULL AND NEW.vpn_local_address!=OLD.vpn_local_address) THEN
    INSERT INTO {{%player_vpn_history}} ({{%player_id}},{{%vpn_local_address}},{{%vpn_remote_address}}) VALUES (NEW.id,NEW.vpn_local_address,NEW.vpn_remote_address);
  END IF;
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
