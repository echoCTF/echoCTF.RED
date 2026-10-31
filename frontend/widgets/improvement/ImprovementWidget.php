<?php

/**
 * Player improvement widget.
 *
 * Compares the player with themselves over rolling periods: the last 7, 30
 * and 365 days against the 7, 30 and 365 days before them.
 *
 * Metrics:
 *  * points, flags (treasures), findings, headshots and challenge solves,
 *    higher is better
 *  * solve pace: how much faster than the median player the player solves
 *    the targets they headshot (median of `target median timer / timer`),
 *    higher is better. Headshots made with writeups activated for the target
 *    are left out, and only targets with at least MIN_TARGET_SOLVES timed
 *    headshots count, since the median of a few solves is noise.
 */

namespace app\widgets\improvement;

use Yii;
use yii\base\Widget;

class ImprovementWidget extends Widget
{
  /** Targets need this many headshots for their median to count */
  const MIN_TARGET_SOLVES = 5;

  /** @var int the player to report on */
  public $player_id;

  /** @var int seconds the numbers are cached for */
  public $cacheDuration = 900;

  /** @var array period key => [label, days] */
  public static $periods = [
    'week' => ['Week', 7],
    'month' => ['Month', 30],
    'year' => ['Year', 365],
  ];

  public function run()
  {
    if (Yii::$app->sys->profile_improvements_disabled !== false) {
      return '';
    }

    $stats = Yii::$app->cache->getOrSet('player_improvement:' . intval($this->player_id), function () {
      return $this->collect();
    }, $this->cacheDuration);

    return $this->render('_card', ['stats' => $stats, 'periods' => self::$periods]);
  }

  /**
   * Collects every metric for every period.
   * @return array metric => [label, period => [current, previous]]
   */
  protected function collect()
  {
    $p = intval($this->player_id);
    return [
      'points' => [Yii::t('app', 'Points'), $this->sums('SELECT ts AS at, points AS v FROM stream WHERE player_id=:p', $p)],
      'flags' => [Yii::t('app', 'Flags'), $this->sums('SELECT ts AS at, 1 AS v FROM player_treasure WHERE player_id=:p', $p)],
      'findings' => [Yii::t('app', 'Findings'), $this->sums('SELECT ts AS at, 1 AS v FROM player_finding WHERE player_id=:p', $p)],
      'headshots' => [Yii::t('app', 'Headshots'), $this->sums('SELECT created_at AS at, 1 AS v FROM headshot WHERE player_id=:p', $p)],
      'challenges' => [Yii::t('app', 'Challenge solves'), $this->sums('SELECT created_at AS at, 1 AS v FROM challenge_solver WHERE player_id=:p', $p)],
      'pace' => [Yii::t('app', 'Solve pace'), $this->pace($p)],
    ];
  }

  /**
   * Sums `v` of the rows of $sql in each current and previous period.
   * @param string $sql selects `at` (timestamp) and `v` (value) for player :p
   * @param int $player_id
   * @return array period => [current, previous]
   */
  protected function sums(string $sql, int $player_id): array
  {
    $columns = [];
    foreach (self::$periods as $key => [, $days]) {
      $days = intval($days);
      $columns[] = "COALESCE(SUM(IF(at >= NOW() - INTERVAL $days DAY, v, 0)),0) AS {$key}_cur";
      $columns[] = "COALESCE(SUM(IF(at < NOW() - INTERVAL $days DAY AND at >= NOW() - INTERVAL " . ($days * 2) . " DAY, v, 0)),0) AS {$key}_prev";
    }
    $longest = intval(max(array_column(self::$periods, 1))) * 2;
    $row = Yii::$app->db->createCommand(
      'SELECT ' . implode(', ', $columns) . " FROM ($sql) x WHERE at >= NOW() - INTERVAL $longest DAY",
      [':p' => $player_id]
    )->queryOne();

    $result = [];
    foreach (array_keys(self::$periods) as $key) {
      $result[$key] = [(int) $row["{$key}_cur"], (int) $row["{$key}_prev"]];
    }
    return $result;
  }

  /**
   * Median solve pace in each current and previous period.
   * @param int $player_id
   * @return array period => [current, previous], null when there are no solves to judge
   */
  protected function pace(int $player_id): array
  {
    $longest = intval(max(array_column(self::$periods, 1))) * 2;
    // the median timer of each target the player solved, over all its
    // headshots that have a timer (moderators can zero timers out)
    $rows = Yii::$app->db->createCommand(<<<SQL
SELECT UNIX_TIMESTAMP(h.created_at) AS at, m.target_median / NULLIF(h.timer, 0) AS pace
FROM headshot h
JOIN (
  SELECT DISTINCT target_id,
         MEDIAN(timer) OVER (PARTITION BY target_id) AS target_median,
         COUNT(*) OVER (PARTITION BY target_id) AS target_solves
  FROM headshot
  WHERE timer > 0
    AND target_id IN (SELECT target_id FROM headshot WHERE player_id=:p1)
) m ON m.target_id=h.target_id
WHERE h.player_id=:p2
  AND h.created_at >= NOW() - INTERVAL $longest DAY
  AND m.target_solves >= :min_solves
  AND h.timer > 0
  AND NOT EXISTS (SELECT 1 FROM player_target_help pth WHERE pth.player_id=h.player_id AND pth.target_id=h.target_id)
SQL, [':p1' => $player_id, ':p2' => $player_id, ':min_solves' => self::MIN_TARGET_SOLVES])->queryAll();

    $now = time();
    $result = [];
    foreach (self::$periods as $key => [, $days]) {
      $cur = $prev = [];
      foreach ($rows as $row) {
        $age = $now - (int) $row['at'];
        if ($row['pace'] === null) {
          continue;
        }
        if ($age <= $days * 86400) {
          $cur[] = (float) $row['pace'];
        } elseif ($age <= $days * 2 * 86400) {
          $prev[] = (float) $row['pace'];
        }
      }
      $result[$key] = [self::median($cur), self::median($prev)];
    }
    return $result;
  }

  /**
   * @param float[] $values
   * @return float|null
   */
  protected static function median(array $values)
  {
    if ($values === []) {
      return null;
    }
    sort($values);
    $n = count($values);
    $mid = intdiv($n, 2);
    return $n % 2 ? $values[$mid] : ($values[$mid - 1] + $values[$mid]) / 2;
  }
}
