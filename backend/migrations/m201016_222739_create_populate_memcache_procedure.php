<?php

use yii\db\Migration;

/**
 * Class m201016_222739_create_populate_memcache_procedure
 */
class m201016_222739_create_populate_memcache_procedure extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%populate_memcache}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%populate_memcache}} ()
  BEGIN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    INSERT INTO devnull SELECT memc_set(CONCAT('player:',id),id) FROM player;
    INSERT INTO devnull SELECT memc_set(CONCAT('player_type:',id),`type`) FROM player;
    INSERT INTO devnull SELECT memc_set(CONCAT('team_player:',player_id),team_id) FROM team_player;
    INSERT INTO devnull SELECT memc_set(CONCAT('team_finding:',t2.team_id, ':', t1.finding_id),t1.player_id) FROM player_finding AS t1 LEFT JOIN team_player AS t2 ON t2.player_id=t1.player_id;
    INSERT INTO devnull SELECT memc_set(CONCAT('player_finding:',player_id, ':', finding_id),player_id) FROM player_finding;
    INSERT INTO devnull SELECT memc_set(CONCAT('target:',ip),id) FROM target;
    INSERT INTO devnull SELECT memc_set(CONCAT('target:',id),ip) FROM target;
    INSERT INTO devnull SELECT memc_set(CONCAT('sysconfig:',id),val) FROM sysconfig;
    INSERT INTO devnull SELECT memc_set(CONCAT('finding:',protocol,':',ifnull(port,0), ':', target_id ),id) FROM finding;
  END";
    // Use up()/down() to run migration code without a transaction.
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
