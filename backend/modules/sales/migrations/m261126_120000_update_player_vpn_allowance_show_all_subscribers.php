<?php

use yii\db\Migration;

/**
 * Include every active subscription in the `player_vpn_allowance` view, even
 * when the product has no `vpn_ip_limit` metadata (those get a limit of 0).
 * VPN_LOGIN uses the existence of a row to skip the IP limit for subscribers.
 */
class m261126_120000_update_player_vpn_allowance_show_all_subscribers extends Migration
{
  public $DROP_SQL = "DROP VIEW IF EXISTS {{%player_vpn_allowance}}";
  public $CREATE_SQL = "CREATE VIEW {{%player_vpn_allowance}} AS
SELECT player_id, MAX(vpn_ip_limit) AS vpn_ip_limit FROM (
  SELECT ps.player_id, COALESCE(CAST(JSON_VALUE(p.metadata, '$.vpn_ip_limit') AS UNSIGNED), 0) AS vpn_ip_limit
    FROM player_subscription ps
    LEFT JOIN price pr ON pr.id=ps.price_id
    LEFT JOIN product p ON p.id=pr.product_id
    WHERE ps.active=1
  UNION ALL
  SELECT pp.player_id, CAST(JSON_VALUE(p.metadata, '$.vpn_ip_limit') AS UNSIGNED) AS vpn_ip_limit
    FROM player_product pp
    JOIN product p ON p.id=pp.product_id
    WHERE pp.ending>NOW() AND CAST(JSON_VALUE(p.metadata, '$.vpn_ip_limit') AS UNSIGNED)>0
) AS allowances
GROUP BY player_id";

  // Previous definition (m261030_120000)
  public $OLD_SQL = "CREATE VIEW {{%player_vpn_allowance}} AS
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
