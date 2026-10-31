<?php

namespace app\modules\moderation\models;

/**
 * Writeup relay candidate claims by IP.
 *
 * One row per treasure claim: a zero point player (fake) holds writeups for a
 * target, another account (beneficiary) shares an IP with the fake and claimed
 * a treasure on the same target after the activation. points next to base
 * shows whether the claim got full points (no writeup halving).
 */
class WriteupRelayClaimSearch extends WriteupRelaySearch
{
  protected function bodySql(array &$bind): string
  {
    $from = $this->activatedFrom('pth.created_at', ':wr_from1', $bind);
    return <<<SQL
SELECT pth.player_id AS fake_id, f.username AS fake, y.id AS beneficiary_id, y.username AS beneficiary,
       pt.treasure_id, tr.target_id, t.name AS target, pt.ts AS claimed_at, pth.created_at AS activated_at, pt.points, tr.points AS base
FROM player_target_help pth
JOIN player f ON f.id=pth.player_id
JOIN player_last fl ON fl.id=f.id
JOIN player_last yl ON yl.id<>f.id AND (
     yl.signup_ip=fl.signup_ip OR yl.signin_ip=fl.signin_ip OR (yl.vpn_remote_address IS NOT NULL AND yl.vpn_remote_address=fl.signin_ip))
JOIN player y ON y.id=yl.id
JOIN treasure tr ON tr.target_id=pth.target_id
JOIN target t ON t.id=tr.target_id
JOIN player_treasure pt ON pt.treasure_id=tr.id AND pt.player_id=y.id AND pt.ts>pth.created_at
WHERE NOT EXISTS (SELECT 1 FROM player_target_help p2 WHERE p2.player_id=y.id AND p2.target_id=pth.target_id)
  AND NOT EXISTS (SELECT 1 FROM player_score ps WHERE ps.player_id=f.id AND ps.points>0){$from}
SQL;
  }

  protected function sortConfig(): array
  {
    return [
      'defaultOrder' => ['claimed_at' => SORT_ASC],
      'attributes' => ['fake', 'beneficiary', 'target', 'claimed_at', 'activated_at', 'points', 'base'],
    ];
  }
}
