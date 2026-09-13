<?php

use yii\db\Migration;

class m260913_062949_create_procedure_VPN_BANDWIDTH_LOG extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%VPN_BANDWIDTH_LOG}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%VPN_BANDWIDTH_LOG}}(IN usid BIGINT, IN assignedIP INT UNSIGNED, IN bytesReceived BIGINT UNSIGNED, IN bytesSent BIGINT UNSIGNED, IN sessionDuration INT UNSIGNED)
BEGIN
  IF (SELECT COUNT(*) FROM player WHERE id=usid AND status=10)>0 THEN
    INSERT INTO player_bandwidth (player_id, vpn_local_address, bytes_received, bytes_sent, duration) VALUES (usid, assignedIP, bytesReceived, bytesSent, sessionDuration);
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