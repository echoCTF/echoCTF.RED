<?php

use yii\db\Migration;

/**
 * Class m191118_083150_insert_default_network_players
 */
class m191118_083150_insert_default_network_players extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->db->createCommand("INSERT INTO {{%network_player}} (network_id,player_id,created_at,updated_at) SELECT :network_id,id,NOW(),NOW() FROM {{%player}}")->bindValue(':network_id',1)->execute();
    $this->db->createCommand("INSERT INTO {{%network_player}} (network_id,player_id,created_at,updated_at) SELECT :network_id,id,NOW(),NOW() FROM {{%player}}")->bindValue(':network_id',2)->execute();
    $this->db->createCommand("INSERT INTO {{%network_player}} (network_id,player_id,created_at,updated_at) SELECT :network_id,id,NOW(),NOW() FROM {{%player}}")->bindValue(':network_id',3)->execute();
    $this->db->createCommand("INSERT INTO {{%network_player}} (network_id,player_id,created_at,updated_at) SELECT :network_id,id,NOW(),NOW() FROM {{%player}}")->bindValue(':network_id',4)->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->db->createCommand("DELETE FROM {{%network_player}} WHERE network_id=1")->execute();
    $this->db->createCommand("DELETE FROM {{%network_player}} WHERE network_id=2")->execute();
    $this->db->createCommand("DELETE FROM {{%network_player}} WHERE network_id=3")->execute();
    $this->db->createCommand("DELETE FROM {{%network_player}} WHERE network_id=4")->execute();
  }

}
