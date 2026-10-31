<?php

namespace app\modules\moderation\models;

/**
 * Writeup relay detection without IPs.
 *
 * Pairs a zero point player (fake) that activated writeups for a target with
 * any player that scored on that target within `window_days` days after the
 * activation (treasures and findings). A pair is listed only when it has at
 * least one identity link. Timing and behavior signals only add to the score.
 *
 * Identity links (names are compared with everything but letters removed,
 * case and accent insensitive, at least 5 letters):
 *  * username   score 2   johndoe2 = john_doe
 *  * email      score 3   same email local part, +tag and dots ignored
 *  * fullname   score 3   same fullname
 *  * social:X   score 3   same non-empty discord, twitter, github, htb, youtube or twitch
 *  * referral   score 3   one referred the other (either direction)
 * Boosters (score 1 each, never enough alone):
 *  * fake_never_vpn  the fake never connected to the VPN
 *  * new_on_target   first ever claim of the beneficiary on the target is after the activation
 *  * fast_6h         first claim within 6 hours of the activation
 */
class WriteupRelayIdentitySearch extends WriteupRelaySearch
{
  /** @var int days after the activation in which claims count */
  public $window_days = 7;
  /** @var int only pairs with at least this score */
  public $min_score;

  public function rules()
  {
    return array_merge(parent::rules(), [
      [['window_days'], 'default', 'value' => 7],
      [['window_days'], 'integer', 'min' => 1, 'max' => 365],
      [['min_score'], 'integer', 'min' => 0, 'max' => 20],
    ]);
  }

  public function attributeLabels()
  {
    return array_merge(parent::attributeLabels(), [
      'window_days' => 'Claims within (days)',
      'min_score' => 'Min score',
    ]);
  }

  protected function prefixSql(array &$bind): string
  {
    $bind[':wr_w1'] = (int) $this->window_days;
    $bind[':wr_w2'] = (int) $this->window_days;
    // nowdoc keeps the regex backslashes exactly as the SQL needs them
    $sql = <<<'SQL'
WITH fake AS (
  SELECT pth.player_id, pth.target_id, pth.created_at AS activated_at
  FROM player_target_help pth
  WHERE NOT EXISTS (SELECT 1 FROM player_score ps WHERE ps.player_id=pth.player_id AND ps.points>0)/*FROM*/
),
pairs AS (
  SELECT fake_id, beneficiary_id, target_id, activated_at,
         COUNT(*) AS claims, SUM(points) AS claim_points, MIN(ts) AS first_claim
  FROM (
    SELECT k.player_id AS fake_id, c.player_id AS beneficiary_id, k.target_id, k.activated_at, c.ts, c.points
    FROM fake k
    JOIN treasure i ON i.target_id=k.target_id
    JOIN player_treasure c ON c.treasure_id=i.id AND c.player_id<>k.player_id AND c.ts>k.activated_at AND c.ts<=k.activated_at+INTERVAL :wr_w1 DAY
    UNION ALL
    SELECT k.player_id, c.player_id, k.target_id, k.activated_at, c.ts, c.points
    FROM fake k
    JOIN finding i ON i.target_id=k.target_id
    JOIN player_finding c ON c.finding_id=i.id AND c.player_id<>k.player_id AND c.ts>k.activated_at AND c.ts<=k.activated_at+INTERVAL :wr_w2 DAY
  ) u
  WHERE NOT EXISTS (SELECT 1 FROM player_target_help p2 WHERE p2.player_id=u.beneficiary_id AND p2.target_id=u.target_id)
  GROUP BY fake_id, beneficiary_id, target_id, activated_at
),
flags AS (
  SELECT p.*, f.username AS fake, y.username AS beneficiary, t.name AS target,
    COALESCE((CHAR_LENGTH(REGEXP_REPLACE(LOWER(f.username),'[^\\p{L}]',''))>=5
      AND REGEXP_REPLACE(LOWER(f.username),'[^\\p{L}]','')=REGEXP_REPLACE(LOWER(y.username),'[^\\p{L}]','')),0) AS s_username,
    COALESCE((CHAR_LENGTH(REGEXP_REPLACE(LOWER(SUBSTRING_INDEX(SUBSTRING_INDEX(f.email,'@',1),'+',1)),'[^\\p{L}]',''))>=5
      AND REGEXP_REPLACE(LOWER(SUBSTRING_INDEX(SUBSTRING_INDEX(f.email,'@',1),'+',1)),'[^\\p{L}]','')=REGEXP_REPLACE(LOWER(SUBSTRING_INDEX(SUBSTRING_INDEX(y.email,'@',1),'+',1)),'[^\\p{L}]','')),0) AS s_email,
    COALESCE((CHAR_LENGTH(REGEXP_REPLACE(LOWER(f.fullname),'[^\\p{L}]',''))>=5
      AND REGEXP_REPLACE(LOWER(f.fullname),'[^\\p{L}]','')=REGEXP_REPLACE(LOWER(y.fullname),'[^\\p{L}]','')),0) AS s_fullname,
    CONCAT_WS(',',
      IF(TRIM(pf.discord)<>'' AND LOWER(TRIM(pf.discord))=LOWER(TRIM(py.discord)),'discord',NULL),
      IF(TRIM(pf.twitter)<>'' AND LOWER(TRIM(pf.twitter))=LOWER(TRIM(py.twitter)),'twitter',NULL),
      IF(TRIM(pf.github)<>'' AND LOWER(TRIM(pf.github))=LOWER(TRIM(py.github)),'github',NULL),
      IF(TRIM(pf.htb)<>'' AND LOWER(TRIM(pf.htb))=LOWER(TRIM(py.htb)),'htb',NULL),
      IF(TRIM(pf.youtube)<>'' AND LOWER(TRIM(pf.youtube))=LOWER(TRIM(py.youtube)),'youtube',NULL),
      IF(TRIM(pf.twitch)<>'' AND LOWER(TRIM(pf.twitch))=LOWER(TRIM(py.twitch)),'twitch',NULL)) AS s_social,
    EXISTS (SELECT 1 FROM player_relation r WHERE (r.player_id=p.fake_id AND r.referred_id=p.beneficiary_id) OR (r.player_id=p.beneficiary_id AND r.referred_id=p.fake_id)) AS s_referral,
    (COALESCE(fl.on_vpn<'1971-01-01',1) AND NOT EXISTS (SELECT 1 FROM player_vpn_history h WHERE h.player_id=p.fake_id)) AS b_never_vpn,
    COALESCE(tps.created_at>p.activated_at,1) AS b_new,
    (p.first_claim<=p.activated_at+INTERVAL 6 HOUR) AS b_fast
  FROM pairs p
  JOIN player f ON f.id=p.fake_id
  JOIN player y ON y.id=p.beneficiary_id
  JOIN target t ON t.id=p.target_id
  LEFT JOIN profile pf ON pf.player_id=f.id
  LEFT JOIN profile py ON py.player_id=y.id
  LEFT JOIN player_last fl ON fl.id=f.id
  LEFT JOIN target_player_state tps ON tps.id=p.target_id AND tps.player_id=p.beneficiary_id
)
SQL;
    return str_replace('/*FROM*/', $this->activatedFrom('pth.created_at', ':wr_from1', $bind), $sql);
  }

  protected function bodySql(array &$bind): string
  {
    return <<<'SQL'
SELECT fake_id, fake, beneficiary_id, beneficiary, target_id, target,
       2*s_username+3*s_email+3*s_fullname+3*(s_social<>'')+3*s_referral+b_never_vpn+b_new+b_fast AS score,
       CONCAT_WS(' ',
         IF(s_username,'username',NULL), IF(s_email,'email',NULL), IF(s_fullname,'fullname',NULL),
         IF(s_social<>'',CONCAT('social:',s_social),NULL), IF(s_referral,'referral',NULL),
         IF(b_never_vpn,'fake_never_vpn',NULL), IF(b_new,'new_on_target',NULL), IF(b_fast,'fast_6h',NULL)) AS signals,
       activated_at, first_claim, claims, claim_points
FROM flags
WHERE s_username OR s_email OR s_fullname OR s_social<>'' OR s_referral
SQL;
  }

  protected function extraWhere(array &$bind): array
  {
    if ($this->min_score === null || $this->min_score === '') {
      return [];
    }
    $bind[':wr_min_score'] = (int) $this->min_score;
    return ['r.score >= :wr_min_score'];
  }

  protected function sortConfig(): array
  {
    return [
      'defaultOrder' => ['score' => SORT_DESC, 'activated_at' => SORT_DESC],
      'attributes' => ['score', 'fake', 'beneficiary', 'target', 'activated_at', 'first_claim', 'claims', 'claim_points'],
    ];
  }
}
