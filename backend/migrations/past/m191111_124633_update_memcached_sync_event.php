<?php

use yii\db\Migration;

/**
 * Class m191111_124633_update_memcached_sync_event
 */
class m191111_124633_update_memcached_sync_event extends Migration
{
  public $DROP_SQL="DROP EVENT IF EXISTS {{%memcached_sync}}";

    public function up()
    {
      // CREATE EVENT ev_player_hint ON SCHEDULE EVERY 1 minute COMMENT 'Keep the users informed based on certain criteria' DO

      $CREATE_SQL="CREATE EVENT {{%memcached_sync}} ON SCHEDULE EVERY 10 SECOND COMMENT 'Sync memcached data to a table so that we dont loose last_seen and other similar information.' DO
  BEGIN
    IF (select memc_server_count()<1) THEN
      select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    INSERT INTO {{%player_last}} ({{%id}},{{%on_pui}}) SELECT {{%id}},from_unixtime(memc_get(concat('last_seen:',id))) as last_seen FROM {{%player}} WHERE {{%active}}=1 HAVING last_seen IS NOT NULL ON DUPLICATE KEY UPDATE {{%on_pui}}=values({{%on_pui}});
    INSERT INTO {{%player_last}} ({{%id}},{{%on_vpn}}) SELECT {{%id}},now() FROM {{%player}} WHERE {{%active}}=1 HAVING memc_get(concat('ovpn:',id)) IS NOT NULL ON DUPLICATE KEY UPDATE on_vpn=values(on_vpn);
  END
  ";
        $this->db->createCommand($this->DROP_SQL)->execute();
        $this->db->createCommand($CREATE_SQL)->execute();


    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
    }
}
