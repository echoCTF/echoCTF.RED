<?php

use yii\db\Migration;

class m261024_172600_add_player_and_vpn_history_indexes extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->db->createCommand('ALTER TABLE player_vpn_history ADD INDEX idx_pvh_vpn_remote_address (vpn_remote_address), ADD INDEX idx_pvh_player_id (player_id)')->execute();
    $this->db->createCommand('ALTER TABLE player ADD INDEX idx_player_academic (academic), ADD INDEX idx_player_email (email)')->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->db->createCommand('ALTER TABLE player_vpn_history DROP INDEX idx_pvh_vpn_remote_address, DROP INDEX idx_pvh_player_id')->execute();
    $this->db->createCommand('ALTER TABLE player DROP INDEX idx_player_academic, DROP INDEX idx_player_email')->execute();
  }
}
