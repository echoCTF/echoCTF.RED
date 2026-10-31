<?php

namespace app\modules\moderation\models;

use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;

/**
 * Fast solves.
 *
 * Headshots solved much faster than the typical player solves the same target.
 * The headshot `timer` is the time between the first and the last claim of the
 * player on the target. Each solve is compared with the median timer of the
 * target (`ratio`, 0.02 means 50 times faster) and with how fast the player
 * usually is (`player_usual_ratio`, the median of their ratios), so players
 * that are fast everywhere are not listed.
 *
 * Medians are taken over all the headshots of a target, and only targets with
 * at least `min_target_solves` headshots are judged. Requires MariaDB for the
 * MEDIAN() window function.
 */
class FastSolveSearch extends Model
{
  /** @var string player username filter (LIKE) */
  public $username;
  /** @var string target name filter (LIKE) */
  public $target;
  /** @var string Y-m-d, only headshots on or after this day */
  public $since;
  /** @var int only targets with at least this many headshots */
  public $min_target_solves = 10;
  /** @var float list solves with a ratio below this (0.1 = 10 times faster) */
  public $max_ratio = 0.1;
  /** @var float skip players whose usual ratio is at or below this (they are fast everywhere) */
  public $usual_ratio = 0.5;
  /** @var int players with fewer judged solves are always listed */
  public $new_player_solves = 5;
  /** @var int traffic below this many bytes makes a fast solve stronger */
  public $min_bytes = 10240;
  /** @var string '', '1' only with writeups, '0' only without writeups */
  public $writeups;
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
      [['min_target_solves'], 'default', 'value' => 10],
      [['min_target_solves'], 'integer', 'min' => 2],
      [['max_ratio'], 'default', 'value' => 0.1],
      [['max_ratio'], 'number', 'min' => 0, 'max' => 1],
      [['usual_ratio'], 'default', 'value' => 0.5],
      [['usual_ratio'], 'number', 'min' => 0],
      [['new_player_solves'], 'default', 'value' => 5],
      [['new_player_solves'], 'integer', 'min' => 0],
      [['min_bytes'], 'default', 'value' => 10240],
      [['min_bytes'], 'integer', 'min' => 0],
      [['writeups'], 'in', 'range' => ['0', '1']],
      [['player_status'], 'in', 'range' => array_keys(\app\modules\frontend\models\Player::STATUSES)],
      [['player_academic'], 'integer', 'min' => 0, 'max' => 255],
    ];
  }

  public function attributeLabels()
  {
    return [
      'username' => 'Player',
      'target' => 'Target',
      'since' => 'Solved from',
      'min_target_solves' => 'Min target solves',
      'max_ratio' => 'Ratio below',
      'usual_ratio' => 'Skip if usual ratio at most',
      'new_player_solves' => 'Always list players with fewer solves than',
      'min_bytes' => 'Little traffic below (bytes)',
      'writeups' => 'Writeups',
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
      ':fs_min_solves' => (int) $this->min_target_solves,
      ':fs_max_ratio' => (float) $this->max_ratio,
      ':fs_usual' => (float) $this->usual_ratio,
      ':fs_new' => (int) $this->new_player_solves,
      ':fs_min_bytes' => (int) $this->min_bytes,
    ];

    $prefix = <<<'SQL'
WITH h AS (
  SELECT h.player_id, h.target_id, h.created_at, h.timer,
         MEDIAN(h.timer) OVER (PARTITION BY h.target_id) AS target_median,
         COUNT(*) OVER (PARTITION BY h.target_id) AS target_solves
  FROM headshot h
),
rated AS (
  SELECT h.*, h.timer / NULLIF(h.target_median, 0) AS ratio
  FROM h WHERE h.target_solves >= :fs_min_solves
),
p AS (
  SELECT rated.*,
         MEDIAN(rated.ratio) OVER (PARTITION BY rated.player_id) AS player_usual_ratio,
         COUNT(*) OVER (PARTITION BY rated.player_id) AS player_solves
  FROM rated
)
SQL;

    $body = <<<'SQL'
SELECT p.player_id, pl.username, pl.status AS player_status, pl.academic AS player_academic, pr.id AS player_profile_id,
       p.target_id, t.name AS target, p.created_at AS solved_at, p.timer, p.target_median, p.target_solves,
       p.ratio, p.player_usual_ratio, p.player_solves,
       COALESCE(tr.bytes_sent + tr.bytes_received, 0) AS bytes,
       tr.player_id IS NOT NULL AS has_traffic,
       pth.player_id IS NOT NULL AS had_writeups,
       (pth.player_id IS NULL AND COALESCE(tr.bytes_sent + tr.bytes_received, 0) < :fs_min_bytes) AS strong
FROM p
JOIN player pl ON pl.id = p.player_id
JOIN target t ON t.id = p.target_id
LEFT JOIN profile pr ON pr.player_id = p.player_id
LEFT JOIN player_target_traffic tr ON tr.player_id = p.player_id AND tr.target_id = p.target_id
LEFT JOIN player_target_help pth ON pth.player_id = p.player_id AND pth.target_id = p.target_id
WHERE p.ratio < :fs_max_ratio
  AND (p.player_usual_ratio > :fs_usual OR p.player_solves < :fs_new)
SQL;

    $where = [];
    if ($this->username !== null && $this->username !== '') {
      $where[] = 'r.username LIKE :fs_user';
      $bind[':fs_user'] = '%' . addcslashes($this->username, '%_\\') . '%';
    }
    if ($this->target !== null && $this->target !== '') {
      $where[] = 'r.target LIKE :fs_target';
      $bind[':fs_target'] = '%' . addcslashes($this->target, '%_\\') . '%';
    }
    if ($this->since) {
      // medians still use every solve of the target, only the listed solves are limited
      $where[] = 'r.solved_at >= :fs_since';
      $bind[':fs_since'] = $this->since . ' 00:00:00';
    }
    if ($this->writeups === '0' || $this->writeups === '1') {
      $where[] = 'r.had_writeups = :fs_writeups';
      $bind[':fs_writeups'] = (int) $this->writeups;
    }
    foreach (['player_status', 'player_academic'] as $attribute) {
      if ($this->$attribute !== null && $this->$attribute !== '') {
        $where[] = "r.$attribute = :fs_$attribute";
        $bind[":fs_$attribute"] = (int) $this->$attribute;
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
        'defaultOrder' => ['ratio' => SORT_ASC],
        'attributes' => ['username', 'target', 'solved_at', 'timer', 'target_median', 'ratio', 'player_usual_ratio', 'player_solves', 'bytes'],
      ],
    ]);
  }
}
