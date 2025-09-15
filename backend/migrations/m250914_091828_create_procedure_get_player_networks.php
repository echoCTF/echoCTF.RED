<?php

use yii\db\Migration;

class m250914_091828_create_procedure_get_player_networks extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%get_player_pf_networks}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%get_player_pf_networks}}(IN usid INT)
  BEGIN
    SELECT codename as netname FROM network WHERE (codename IS NOT NULL AND active=1) AND (public=1 or id IN (SELECT network_id FROM network_player WHERE player_id=usid))
    UNION
    SELECT LOWER(CONCAT(t2.name,'_',player_id)) as netname
    FROM target_instance AS t1
    LEFT JOIN target AS t2 ON t1.target_id = t2.id
    WHERE player_id = usid
      OR player_id IN (
        SELECT tp1.player_id
        FROM team_player tp1
        JOIN team_player tp2 ON tp1.team_id = tp2.team_id
        WHERE tp2.player_id = usid
          AND tp1.approved = 1
          AND (
            memc_get('sysconfig:team_visible_instances') IS NOT NULL
            OR team_allowed = 1
          )
      );
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