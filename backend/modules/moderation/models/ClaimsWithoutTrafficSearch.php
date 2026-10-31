<?php

namespace app\modules\moderation\models;

use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;

/**
 * Claims without traffic.
 *
 * Players who scored on a target they never (or barely) sent traffic to, or
 * scored before their first recorded packet to it. Finding a treasure or a
 * finding needs talking to the target over the VPN, so these claims point to
 * flags received from someone else. Traffic comes from player_target_traffic
 * (traffic/collect), which already counts instances and private network
 * targets against their target.
 *
 * Only claims made while traffic accounting was running are checked: from the
 * earliest player_target_traffic record, or `since` when that is later.
 *
 * Signals:
 *  * no_traffic              no traffic at all from the player to the target
 *  * claimed_before_traffic  first claim earlier than the first recorded packet
 *  * under_threshold         some traffic, but less than `min_bytes`
 */
class ClaimsWithoutTrafficSearch extends Model
{
  /** @var string player username filter (LIKE) */
  public $username;
  /** @var string target name filter (LIKE) */
  public $target;
  /** @var string Y-m-d, only claims on or after this day */
  public $since;
  /** @var int traffic below this many bytes counts as too little */
  public $min_bytes = 10240;
  /** @var int|string player status, one of Player::STATUSES */
  public $player_status;
  /** @var int|string player academic grouping */
  public $player_academic;

  public function rules()
  {
    return [
      [['username', 'target'], 'trim'],
      [['username', 'target'], 'string', 'max' => 255],
      [['since'], 'date', 'format' => 'php:Y-m-d'],
      [['min_bytes'], 'default', 'value' => 10240],
      [['min_bytes'], 'integer', 'min' => 0],
      [['player_status'], 'in', 'range' => array_keys(\app\modules\frontend\models\Player::STATUSES)],
      [['player_academic'], 'integer', 'min' => 0, 'max' => 255],
    ];
  }

  public function attributeLabels()
  {
    return [
      'username' => 'Player',
      'target' => 'Target',
      'since' => 'Claims from',
      'min_bytes' => 'Too little below (bytes)',
      'player_status' => 'Status',
      'player_academic' => 'Academic',
    ];
  }

  /**
   * Builds the data provider for the report.
   * @param array $params request parameters
   * @return SqlDataProvider
   */
  public function search($params)
  {
    $this->load($params);
    if (!$this->validate()) {
      // fall back to defaults for anything invalid so the page still renders
      foreach (array_keys($this->getErrors()) as $attribute) {
        $this->$attribute = (new static())->$attribute;
      }
      $this->clearErrors();
    }

    $bind = [
      // never before traffic accounting started, there is no data to compare with
      ':cwt_since' => $this->since ? $this->since . ' 00:00:00' : null,
      ':cwt_min1' => (int) $this->min_bytes,
      ':cwt_min2' => (int) $this->min_bytes,
    ];

    $prefix = <<<'SQL'
WITH since AS (
  SELECT GREATEST(COALESCE(:cwt_since, '1970-01-01 00:00:00'), MIN(first_seen)) AS ts FROM player_target_traffic
),
claims AS (
  SELECT c.player_id, i.target_id, c.ts, c.points
  FROM player_treasure c JOIN treasure i ON i.id=c.treasure_id JOIN since s ON c.ts >= s.ts
  UNION ALL
  SELECT c.player_id, i.target_id, c.ts, c.points
  FROM player_finding c JOIN finding i ON i.id=c.finding_id JOIN since s ON c.ts >= s.ts
),
per AS (
  SELECT player_id, target_id, COUNT(*) AS claims, SUM(points) AS claim_points, MIN(ts) AS first_claim
  FROM claims GROUP BY player_id, target_id
)
SQL;

    $body = <<<'SQL'
SELECT p.player_id, pl.username, pl.status AS player_status, pl.academic AS player_academic, pr.id AS player_profile_id,
       p.target_id, t.name AS target, p.claims, p.claim_points, p.first_claim,
       tr.first_seen AS first_packet,
       COALESCE(tr.bytes_sent+tr.bytes_received,0) AS bytes,
       COALESCE(tr.packets_sent+tr.packets_received,0) AS packets,
       CONCAT_WS(' ',
         IF(tr.player_id IS NULL,'no_traffic',NULL),
         IF(tr.first_seen>p.first_claim,'claimed_before_traffic',NULL),
         IF(tr.player_id IS NOT NULL AND tr.bytes_sent+tr.bytes_received<:cwt_min1,'under_threshold',NULL)) AS signals
FROM per p
JOIN player pl ON pl.id=p.player_id
JOIN target t ON t.id=p.target_id
LEFT JOIN profile pr ON pr.player_id=p.player_id
LEFT JOIN player_target_traffic tr ON tr.player_id=p.player_id AND tr.target_id=p.target_id
WHERE tr.player_id IS NULL OR tr.first_seen>p.first_claim OR tr.bytes_sent+tr.bytes_received<:cwt_min2
SQL;
    $where = [];
    if ($this->username !== null && $this->username !== '') {
      $where[] = 'r.username LIKE :cwt_user';
      $bind[':cwt_user'] = '%' . addcslashes($this->username, '%_\\') . '%';
    }
    if ($this->target !== null && $this->target !== '') {
      $where[] = 'r.target LIKE :cwt_target';
      $bind[':cwt_target'] = '%' . addcslashes($this->target, '%_\\') . '%';
    }
    foreach (['player_status', 'player_academic'] as $attribute) {
      if ($this->$attribute !== null && $this->$attribute !== '') {
        $where[] = "r.$attribute = :cwt_$attribute";
        $bind[":cwt_$attribute"] = (int) $this->$attribute;
      }
    }
    $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

    $from = '(' . $body . ') r' . $whereSql;
    $totalCount = (int) Yii::$app->db->createCommand($prefix . ' SELECT COUNT(*) FROM ' . $from, $bind)->queryScalar();

    return new SqlDataProvider([
      'sql' => $prefix . ' SELECT r.* FROM ' . $from,
      'params' => $bind,
      'totalCount' => $totalCount,
      'pagination' => ['pageSize' => 50],
      'sort' => [
        'defaultOrder' => ['first_claim' => SORT_DESC],
        'attributes' => ['username', 'target', 'claims', 'claim_points', 'first_claim', 'first_packet', 'bytes', 'packets'],
      ],
    ]);
  }
}
