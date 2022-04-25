<?php

use yii\db\Migration;

/**
 * Class m220425_123404_create_tau_player_disabledroute_trigger
 */
class m220425_123404_create_tau_player_disabledroute_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player_disabledroute}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tau_player_disabledroute}} AFTER UPDATE ON {{%player_disabledroute}} FOR EACH ROW
    thisBegin:BEGIN
      DECLARE routes LONGTEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('player_id',player_id,'route', route) ORDER BY player_id),']') FROM player_disabledroute ORDER BY player_id,route);
      INSERT INTO sysconfig (id,val) VALUES ('player_disabled_routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
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
