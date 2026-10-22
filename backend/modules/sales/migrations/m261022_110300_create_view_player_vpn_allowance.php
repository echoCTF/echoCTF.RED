<?php

use yii\db\Migration;

/**
 * Per player VPN connections per remote IP allowance granted by the
 * `vpn_ip_limit` product metadata key.
 */
class m261022_110300_create_view_player_vpn_allowance extends Migration
{
  public $DROP_SQL = "DROP VIEW IF EXISTS {{%player_vpn_allowance}}";
  public $CREATE_SQL = "CREATE VIEW {{%player_vpn_allowance}} AS
SELECT a.player_id, MAX(a.vpn_ip_limit) AS vpn_ip_limit FROM (
  SELECT ps.player_id, CAST(JSON_VALUE(p.metadata, '$.vpn_ip_limit') AS UNSIGNED) AS vpn_ip_limit
  FROM player_subscription ps
  JOIN price pr ON pr.id=ps.price_id
  JOIN product p ON p.id=pr.product_id
  WHERE ps.active=1
  UNION ALL
  /* one-time purchases, remove this branch to only count subscriptions */
  SELECT pp.player_id, CAST(JSON_VALUE(p.metadata, '$.vpn_ip_limit') AS UNSIGNED) AS vpn_ip_limit
  FROM player_product pp
  JOIN product p ON p.id=pp.product_id
  WHERE pp.ending IS NULL OR pp.ending>NOW()
) a WHERE a.vpn_ip_limit>0 GROUP BY a.player_id";

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
