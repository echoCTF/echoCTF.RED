<?php

use yii\db\Migration;

/**
 * Include one-off purchases (player_product) that have not ended yet in the
 * `player_vpn_allowance` view, so products such as the Workshop Pass raise
 * the VPN connections per IP limit while they are active.
 */
class m261030_120000_update_view_player_vpn_allowance extends Migration
{
  public $DROP_SQL = "DROP VIEW IF EXISTS {{%player_vpn_allowance}}";
  public $CREATE_SQL = "CREATE VIEW {{%player_vpn_allowance}} AS
SELECT player_id, MAX(vpn_ip_limit) AS vpn_ip_limit FROM (
  SELECT ps.player_id, CAST(JSON_VALUE(p.metadata, '$.vpn_ip_limit') AS UNSIGNED) AS vpn_ip_limit
    FROM player_subscription ps
    JOIN price pr ON pr.id=ps.price_id
    JOIN product p ON p.id=pr.product_id
    WHERE ps.active=1
  UNION ALL
  SELECT pp.player_id, CAST(JSON_VALUE(p.metadata, '$.vpn_ip_limit') AS UNSIGNED) AS vpn_ip_limit
    FROM player_product pp
    JOIN product p ON p.id=pp.product_id
    WHERE pp.ending>NOW()
) AS allowances
WHERE vpn_ip_limit>0
GROUP BY player_id";

  // Previous definition, subscriptions only
  public $OLD_SQL = "CREATE VIEW {{%player_vpn_allowance}} AS
SELECT ps.player_id, MAX(CAST(JSON_VALUE(p.metadata, '$.vpn_ip_limit') AS UNSIGNED)) AS vpn_ip_limit
  FROM player_subscription ps
  JOIN price pr ON pr.id=ps.price_id
  JOIN product p ON p.id=pr.product_id
  WHERE ps.active=1 AND CAST(JSON_VALUE(p.metadata, '$.vpn_ip_limit') AS UNSIGNED)>0
  GROUP BY ps.player_id";

  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->OLD_SQL)->execute();
  }
}
