<?php

namespace app\modules\target\models;

use Yii;
use yii\base\BaseObject;
use app\modules\game\models\Headshot;
use app\modules\target\models\PlayerTargetHelp as PTH;

/**
 * Encapsulates the rules for activating writeups on a target.
 *
 * Sysconfig keys:
 * * writeup_stuck_hours: hours since the player's last own treasure or
 *   finding on the target before activation is allowed. Unset/0 = off.
 * * writeup_cost_percent: percent of the player's unclaimed points on the
 *   target charged on activation. Unset/0 = off (legacy halving instead).
 *   Only applies while stream_player_target_help is on, since the cost is
 *   charged through the stream.
 * * stream_player_target_help: log activations on the stream. When off,
 *   activations fall back to the legacy halving.
 * * writeup_cost_multiplier: the player's score must be at least this many
 *   times the cost to activate. Unset/0 = 3.
 * * writeups_use_require_xp: when on, the player's score must be at least
 *   target.required_xp to activate.
 * * plus_writeups: extra activations allowed on top of headshots.
 */
class WriteupActivation extends BaseObject
{
  /** @var int */
  public $player_id;

  /** @var Target */
  public $target;

  /** @var bool */
  public $isAdmin = false;

  public static function stuckHours(): int
  {
    return max(0, intval(Yii::$app->sys->writeup_stuck_hours));
  }

  public static function costPercent(): int
  {
    return min(100, max(0, intval(Yii::$app->sys->writeup_cost_percent)));
  }

  /**
   * How many times the cost the player's score must be to activate.
   */
  public static function costMultiplier(): int
  {
    $val = intval(Yii::$app->sys->writeup_cost_multiplier);
    return $val > 0 ? $val : 3;
  }

  /**
   * Minimum score needed to pay the given cost.
   */
  public static function requiredScore(int $cost): int
  {
    return self::costMultiplier() * $cost;
  }

  public static function useRequiredXp(): bool
  {
    return (bool) Yii::$app->sys->writeups_use_require_xp;
  }

  public static function streamEnabled(): bool
  {
    return (bool) Yii::$app->sys->stream_player_target_help;
  }

  /**
   * Whether this activation is a paid one (rules 5 to 7 apply). The cost is
   * charged through the stream, so paid activations need stream logging on.
   */
  public function isPaid(): bool
  {
    return !$this->isAdmin && self::costPercent() > 0 && self::streamEnabled();
  }

  /**
   * Sum of points of treasures and findings on the target that the player
   * has not claimed yet.
   */
  public function unclaimedPoints(): int
  {
    $sql = "SELECT
      (SELECT COALESCE(SUM(t.points),0) FROM treasure t
        WHERE t.target_id=:tid1 AND t.id NOT IN (SELECT treasure_id FROM player_treasure WHERE player_id=:pid1))
     +(SELECT COALESCE(SUM(f.points),0) FROM finding f
        WHERE f.target_id=:tid2 AND f.id NOT IN (SELECT finding_id FROM player_finding WHERE player_id=:pid2))";
    $params = [':tid1' => $this->target->id, ':pid1' => $this->player_id, ':tid2' => $this->target->id, ':pid2' => $this->player_id];
    return intval(Yii::$app->db->createCommand($sql, $params)->queryScalar());
  }

  /**
   * Points charged for this activation. Rounded up so any unclaimed points
   * yield a non zero cost (cost=0 means legacy halving).
   */
  public function cost(): int
  {
    if (!$this->isPaid())
      return 0;
    return intval(ceil($this->unclaimedPoints() * self::costPercent() / 100));
  }

  /**
   * Current player score. Pass $lock=true inside a transaction to hold the
   * player_score row until commit.
   */
  public function score(bool $lock = false): int
  {
    $sql = 'SELECT points FROM player_score WHERE player_id=:pid' . ($lock ? ' FOR UPDATE' : '');
    return intval(Yii::$app->db->createCommand($sql, [':pid' => $this->player_id])->queryScalar());
  }

  /**
   * Rule 4. Returns null when the player has no own treasure or finding on
   * the target, otherwise whether the latest of them is older than
   * writeup_stuck_hours. Compared in SQL to stay in the DB time zone.
   */
  public function isStuck(): ?bool
  {
    $sql = "SELECT MAX(ts) < NOW() - INTERVAL :h HOUR AS stuck, COUNT(*) AS cnt FROM (
        SELECT pt.ts FROM player_treasure pt JOIN treasure t ON t.id=pt.treasure_id WHERE pt.player_id=:pid1 AND t.target_id=:tid1
        UNION ALL
        SELECT pf.ts FROM player_finding pf JOIN finding f ON f.id=pf.finding_id WHERE pf.player_id=:pid2 AND f.target_id=:tid2
      ) AS activity";
    $params = [':h' => self::stuckHours(), ':pid1' => $this->player_id, ':tid1' => $this->target->id, ':pid2' => $this->player_id, ':tid2' => $this->target->id];
    $row = Yii::$app->db->createCommand($sql, $params)->queryOne();
    if (intval($row['cnt']) === 0)
      return null;
    return (bool) $row['stuck'];
  }

  /**
   * Checks every activation rule.
   * @return string|null error message, null when activation is allowed
   */
  public function check(): ?string
  {
    $pid = $this->player_id;
    $tid = $this->target->id;

    if (PTH::findOne(['player_id' => $pid, 'target_id' => $tid]) !== null)
      return Yii::t('app', 'You have already enabled writeups for this target.');

    // Rule 3: finishers read for free, nothing to activate
    if (Headshot::findOne(['player_id' => $pid, 'target_id' => $tid]) !== null)
      return Yii::t('app', 'You already have access to the writeups of this target.');

    // Rule 9
    if ((int) Writeup::find()->where(['target_id' => $tid])->approved()->count() === 0)
      return Yii::t('app', 'There are no writeups for this target.');

    // Rule 11: admins bypass the rest
    if ($this->isAdmin)
      return null;

    // Rule 10
    try {
      Yii::$app->getModule('target')->checkNetwork($this->target);
    } catch (\Throwable $e) {
      return Yii::t('app', "Failed to activate writeups for this target. You don't have access to this network.");
    }

    // Rule 8
    $headshots = intval(Headshot::find()->where(['player_id' => $pid])->count());
    $activations = intval(PTH::find()->where(['player_id' => $pid])->count());
    if ($activations >= ($headshots + intval(Yii::$app->sys->plus_writeups)))
      return Yii::t('app', 'You have activated too many writeups, headshot some of the targets first.');

    // target.required_xp
    if (self::useRequiredXp() && intval($this->target->required_xp) > 0 && $this->score() < intval($this->target->required_xp))
      return Yii::t('app', 'You need at least {xp} points to activate writeups for this target.', ['xp' => number_format($this->target->required_xp)]);

    // Rule 4
    if (self::stuckHours() > 0) {
      $stuck = $this->isStuck();
      if ($stuck === null)
        return Yii::t('app', 'You need to claim at least one flag or finding on this target before activating its writeups.');
      if ($stuck === false)
        return Yii::t('app', 'You can activate writeups for this target {hours,plural,=1{one hour} other{# hours}} after your last flag or finding on it.', ['hours' => self::stuckHours()]);
    }

    // Rules 5 and 6
    if ($this->isPaid()) {
      $cost = $this->cost();
      if ($cost > 0 && $this->score() < self::requiredScore($cost))
        return Yii::t('app', 'Activating writeups for this target costs {cost} points. You need at least {need} points.', ['cost' => number_format($cost), 'need' => number_format(self::requiredScore($cost))]);
    }

    return null;
  }

  /**
   * Text shown in the confirmation dialog before activating.
   */
  public function confirmMessage(): string
  {
    if ($this->isPaid())
      return Yii::t('app', 'Are you sure you want to enable access to writeups for this target? It will cost you {cost} points ({percent}% of your unclaimed points on this target). Remaining flags keep their full points.', ['cost' => number_format($this->cost()), 'percent' => self::costPercent()]);
    return Yii::t('app', 'Are you sure you want to enable access to writeups for this target? Any remaining flags will have their points reduced by 50%.');
  }
}
