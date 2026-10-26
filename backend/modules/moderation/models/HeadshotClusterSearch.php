<?php

namespace app\modules\moderation\models;

use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * HeadshotClusterSearch detects clusters of headshots on the same target.
 *
 * A cluster is a group of headshots on one target that fall in the same bucket:
 *  * `day`   : same calendar day
 *  * `week`  : same ISO week (Monday based)
 *  * `burst` : consecutive headshots no more than `gap` minutes apart (gaps and islands)
 *
 * Every cluster carries signals that help a moderator tell a legit rush
 * (new target, weekend event) apart from flag sharing:
 *  * fast_solves     : headshots with a timer below `fast` seconds
 *  * min_timer       : the fastest headshot in the cluster
 *  * span_seconds    : time between the first and last headshot in the cluster
 *  * ip_overlap      : players beyond the first that share a last signin IP
 *  * vpn_overlap     : players beyond the first that share a last VPN remote IP
 *  * team_overlap    : players beyond the first that belong to the same team
 *  * share           : percentage of all the target headshots that fall in this cluster
 *  * days_since_release : how old the target was when the cluster started
 *
 * @author echoCTF moderation
 */
class HeadshotClusterSearch extends Model
{
  const MODE_DAY = 'day';
  const MODE_WEEK = 'week';
  const MODE_BURST = 'burst';

  /** @var string bucketing mode (day, week, burst) */
  public $mode = self::MODE_DAY;
  /** @var int minimum headshots on the same target for a bucket to be reported */
  public $min_headshots = 3;
  /** @var int burst mode: max minutes between consecutive headshots of the same cluster */
  public $gap = 60;
  /** @var int headshot timer (seconds) below which a solve counts as fast */
  public $fast = 300;
  /** @var string Y-m-d, inclusive */
  public $date_from;
  /** @var string Y-m-d, inclusive */
  public $date_to;
  /** @var int ignore headshots made within this many days of the target release */
  public $skip_launch_days = 0;
  /** @var string target name filter (LIKE) */
  public $target;
  /** @var string only clusters that include a player matching this username (LIKE) */
  public $username;

  public function rules()
  {
    return [
      [['mode'], 'default', 'value' => self::MODE_DAY],
      [['mode'], 'in', 'range' => array_keys(self::modes())],
      [['min_headshots'], 'default', 'value' => 3],
      [['min_headshots'], 'integer', 'min' => 2, 'max' => 10000],
      [['gap'], 'default', 'value' => 60],
      [['gap'], 'integer', 'min' => 1, 'max' => 10080],
      [['fast'], 'default', 'value' => 300],
      [['fast'], 'integer', 'min' => 0],
      [['skip_launch_days'], 'default', 'value' => 0],
      [['skip_launch_days'], 'integer', 'min' => 0, 'max' => 3650],
      [['date_from'], 'default', 'value' => date('Y-m-d', strtotime('-30 days'))],
      [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
      [['target', 'username'], 'string', 'max' => 255],
      [['target', 'username'], 'trim'],
    ];
  }

  public function attributeLabels()
  {
    return [
      'mode' => 'Cluster by',
      'min_headshots' => 'Min headshots',
      'gap' => 'Burst gap (minutes)',
      'fast' => 'Fast solve below (seconds)',
      'date_from' => 'From',
      'date_to' => 'To',
      'skip_launch_days' => 'Ignore first N days after release',
      'target' => 'Target',
      'username' => 'Includes player',
    ];
  }

  public static function modes()
  {
    return [
      self::MODE_DAY => 'Day',
      self::MODE_WEEK => 'Week',
      self::MODE_BURST => 'Burst (gap based)',
    ];
  }

  /**
   * Builds the data provider with the clusters.
   * @param array $params request parameters
   * @return ActiveDataProvider
   */
  public function search($params)
  {
    $this->load($params);
    if (!$this->validate()) {
      // fall back to safe defaults for anything invalid so the page still renders
      foreach (array_keys($this->getErrors()) as $attribute) {
        $this->$attribute = (new static())->$attribute;
      }
      $this->validate();
    }

    // player lists can get long for popular targets
    Yii::$app->db->createCommand('SET SESSION group_concat_max_len = 65535')->execute();

    $query = $this->buildQuery();
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'key' => function ($row) {
        return $row['target_id'] . '-' . $row['bucket'];
      },
      'pagination' => ['pageSize' => 50],
      'sort' => [
        'defaultOrder' => ['headshots' => SORT_DESC, 'first_at' => SORT_DESC],
        'attributes' => [
          'target_name',
          'headshots',
          'first_at',
          'span_seconds',
          'avg_timer',
          'min_timer',
          'fast_solves',
          'ip_overlap',
          'vpn_overlap',
          'team_overlap',
          'share',
          'days_since_release',
        ],
      ],
    ]);

    return $dataProvider;
  }

  /**
   * Inner query: the filtered headshots, each tagged with the bucket it belongs to.
   * @return Query
   */
  protected function bucketedHeadshots()
  {
    $base = (new Query())
      ->select(['h.player_id', 'h.target_id', 'h.created_at', 'h.timer'])
      ->from(['h' => 'headshot'])
      ->innerJoin(['t' => 'target'], 't.id=h.target_id')
      ->where(['IS NOT', 'h.created_at', null]);

    if ($this->date_from) {
      $base->andWhere(['>=', 'h.created_at', $this->date_from . ' 00:00:00']);
    }
    if ($this->date_to) {
      $base->andWhere(['<', 'h.created_at', new Expression('DATE_ADD(:hc_to, INTERVAL 1 DAY)', [':hc_to' => $this->date_to])]);
    }
    if ((int) $this->skip_launch_days > 0) {
      $base->andWhere(new Expression(
        'h.created_at >= DATE_ADD(COALESCE(t.scheduled_at, t.created_at, h.created_at), INTERVAL :hc_skip DAY)',
        [':hc_skip' => (int) $this->skip_launch_days]
      ));
    }
    if ($this->target !== null && $this->target !== '') {
      $base->andWhere(['like', 't.name', $this->target]);
    }

    switch ($this->mode) {
      case self::MODE_WEEK:
        $base->addSelect(['bucket' => new Expression("DATE_FORMAT(DATE_SUB(DATE(h.created_at), INTERVAL WEEKDAY(h.created_at) DAY), '%Y-%m-%d')")]);
        return $base;

      case self::MODE_BURST:
        // gaps and islands: a headshot starts a new cluster when it comes more
        // than `gap` minutes after the previous headshot on the same target
        $base->addSelect(['new_cluster' => new Expression(
          'CASE WHEN TIMESTAMPDIFF(SECOND, LAG(h.created_at) OVER (PARTITION BY h.target_id ORDER BY h.created_at, h.player_id), h.created_at) <= :hc_gap THEN 0 ELSE 1 END',
          [':hc_gap' => (int) $this->gap * 60]
        )]);
        return (new Query())
          ->select(['x.player_id', 'x.target_id', 'x.created_at', 'x.timer'])
          ->addSelect(['bucket' => new Expression('SUM(x.new_cluster) OVER (PARTITION BY x.target_id ORDER BY x.created_at, x.player_id)')])
          ->from(['x' => $base]);

      case self::MODE_DAY:
      default:
        $base->addSelect(['bucket' => new Expression("DATE_FORMAT(h.created_at, '%Y-%m-%d')")]);
        return $base;
    }
  }

  /**
   * Outer query: one row per (target, bucket) with the cluster signals.
   * @return Query
   */
  protected function buildQuery()
  {
    $query = (new Query())
      ->select([
        'b.target_id',
        'b.bucket',
        'target_name' => 't.name',
        'target_ip' => 'INET_NTOA(t.ip)',
        'headshots' => new Expression('COUNT(*)'),
        'first_at' => new Expression('MIN(b.created_at)'),
        'last_at' => new Expression('MAX(b.created_at)'),
        'span_seconds' => new Expression('TIMESTAMPDIFF(SECOND, MIN(b.created_at), MAX(b.created_at))'),
        'avg_timer' => new Expression('ROUND(AVG(b.timer))'),
        'min_timer' => new Expression('MIN(b.timer)'),
        'fast_solves' => new Expression('SUM(CASE WHEN b.timer < :hc_fast THEN 1 ELSE 0 END)', [':hc_fast' => (int) $this->fast]),
        'ip_overlap' => new Expression('COUNT(pl.signin_ip) - COUNT(DISTINCT pl.signin_ip)'),
        'vpn_overlap' => new Expression('COUNT(pl.vpn_remote_address) - COUNT(DISTINCT pl.vpn_remote_address)'),
        'team_overlap' => new Expression('COUNT(tp.team_id) - COUNT(DISTINCT tp.team_id)'),
        'target_total' => new Expression('MAX(ts.total_headshots)'),
        'share' => new Expression('ROUND(COUNT(*) * 100 / NULLIF(MAX(ts.total_headshots), 0), 1)'),
        'days_since_release' => new Expression('DATEDIFF(MIN(b.created_at), COALESCE(MAX(t.scheduled_at), MAX(t.created_at)))'),
        'players' => new Expression("GROUP_CONCAT(CONCAT(p.id, ':', p.username) ORDER BY b.created_at, p.id SEPARATOR ',')"),
      ])
      ->from(['b' => $this->bucketedHeadshots()])
      ->innerJoin(['t' => 'target'], 't.id=b.target_id')
      ->innerJoin(['p' => 'player'], 'p.id=b.player_id')
      ->leftJoin(['pl' => 'player_last'], 'pl.id=b.player_id')
      ->leftJoin(['tp' => 'team_player'], 'tp.player_id=b.player_id AND tp.approved=1')
      ->leftJoin(['ts' => 'target_state'], 'ts.id=b.target_id')
      ->groupBy(['b.target_id', 'b.bucket', 't.name', 't.ip'])
      ->having(['>=', new Expression('COUNT(*)'), (int) $this->min_headshots]);

    if ($this->username !== null && $this->username !== '') {
      $query->andHaving(new Expression('SUM(CASE WHEN p.username LIKE :hc_user THEN 1 ELSE 0 END) > 0', [':hc_user' => '%' . addcslashes($this->username, '%_\\') . '%']));
    }

    return $query;
  }

  /**
   * Splits the `players` column into [player_id => username].
   * @param string|null $players
   * @return array
   */
  public static function parsePlayers($players)
  {
    $result = [];
    if (empty($players)) {
      return $result;
    }
    foreach (explode(',', $players) as $entry) {
      $parts = explode(':', $entry, 2);
      if (count($parts) === 2) {
        $result[(int) $parts[0]] = $parts[1];
      }
    }
    return $result;
  }
}
