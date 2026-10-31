<?php

namespace app\modules\moderation\models;

/**
 * Writeup relay detection by IP.
 *
 * A zero point player (fake) activated writeups for a target, and another
 * player (beneficiary) sharing an IP with the fake scored on the same target
 * afterwards (treasures and findings). One row per fake, beneficiary, target,
 * activation and matched IPs.
 *
 * matched lists which IPs matched, labeled by field:
 *  * signup:IP  both players have this signup_ip
 *  * signin:IP  both players have this signin_ip
 *  * vpn:IP     beneficiary vpn_remote_address equals the fake signin_ip
 */
class WriteupRelayIpSearch extends WriteupRelaySearch
{
  protected function bodySql(array &$bind): string
  {
    $from1 = $this->activatedFrom('pth.created_at', ':wr_from1', $bind);
    $from2 = $this->activatedFrom('pth.created_at', ':wr_from2', $bind);
    return <<<SQL
SELECT fake_id, fake, beneficiary_id, beneficiary, target_id, target, matched, activated_at,
       COUNT(*) AS claims, SUM(points) AS claim_points, MIN(ts) AS first_claim
FROM (
  SELECT pth.player_id AS fake_id, f.username AS fake, y.id AS beneficiary_id, y.username AS beneficiary,
         t.id AS target_id, t.name AS target, pth.created_at AS activated_at, c.ts, c.points,
         CONCAT_WS(' ',
           IF(yl.signup_ip=fl.signup_ip, CONCAT('signup:', INET_NTOA(fl.signup_ip)), NULL),
           IF(yl.signin_ip=fl.signin_ip, CONCAT('signin:', INET_NTOA(fl.signin_ip)), NULL),
           IF(yl.vpn_remote_address=fl.signin_ip, CONCAT('vpn:', INET_NTOA(fl.signin_ip)), NULL)
         ) AS matched
  FROM player_target_help pth
  JOIN player f ON f.id=pth.player_id
  JOIN player_last fl ON fl.id=f.id
  JOIN player_last yl ON yl.id<>f.id AND (yl.signup_ip=fl.signup_ip OR yl.signin_ip=fl.signin_ip OR yl.vpn_remote_address=fl.signin_ip)
  JOIN player y ON y.id=yl.id
  JOIN target t ON t.id=pth.target_id
  JOIN treasure i ON i.target_id=pth.target_id
  JOIN player_treasure c ON c.player_id=y.id AND c.treasure_id=i.id AND c.ts>pth.created_at
  WHERE NOT EXISTS (SELECT 1 FROM player_target_help p2 WHERE p2.player_id=y.id AND p2.target_id=pth.target_id)
    AND NOT EXISTS (SELECT 1 FROM player_score ps WHERE ps.player_id=f.id AND ps.points>0){$from1}
  UNION ALL
  SELECT pth.player_id, f.username, y.id, y.username,
         t.id, t.name, pth.created_at, c.ts, c.points,
         CONCAT_WS(' ',
           IF(yl.signup_ip=fl.signup_ip, CONCAT('signup:', INET_NTOA(fl.signup_ip)), NULL),
           IF(yl.signin_ip=fl.signin_ip, CONCAT('signin:', INET_NTOA(fl.signin_ip)), NULL),
           IF(yl.vpn_remote_address=fl.signin_ip, CONCAT('vpn:', INET_NTOA(fl.signin_ip)), NULL)
         )
  FROM player_target_help pth
  JOIN player f ON f.id=pth.player_id
  JOIN player_last fl ON fl.id=f.id
  JOIN player_last yl ON yl.id<>f.id AND (yl.signup_ip=fl.signup_ip OR yl.signin_ip=fl.signin_ip OR yl.vpn_remote_address=fl.signin_ip)
  JOIN player y ON y.id=yl.id
  JOIN target t ON t.id=pth.target_id
  JOIN finding i ON i.target_id=pth.target_id
  JOIN player_finding c ON c.player_id=y.id AND c.finding_id=i.id AND c.ts>pth.created_at
  WHERE NOT EXISTS (SELECT 1 FROM player_target_help p2 WHERE p2.player_id=y.id AND p2.target_id=pth.target_id)
    AND NOT EXISTS (SELECT 1 FROM player_score ps WHERE ps.player_id=f.id AND ps.points>0){$from2}
) u
GROUP BY fake_id, fake, beneficiary_id, beneficiary, target_id, target, activated_at, matched
SQL;
  }

  protected function sortConfig(): array
  {
    return [
      'defaultOrder' => ['activated_at' => SORT_DESC],
      'attributes' => ['fake', 'beneficiary', 'target', 'activated_at', 'first_claim', 'claims', 'claim_points'],
    ];
  }
}
