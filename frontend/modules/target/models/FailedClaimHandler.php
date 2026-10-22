<?php

namespace app\modules\target\models;

use app\models\Player;

class FailedClaimHandler extends \yii\base\Model
{
  /** Used when the matched treasure has no points set. */
  const PENALTY_FALLBACK_POINTS = -1500;

  /** abuser.reason of a failed claim whose owner lookup timed out; the scan_failed_claims event settles it. */
  const REASON_PENDING = 'failed_claim_pending';

  /**
   * Finds the treasure and owning player behind a submitted flag code.
   *
   * Same prefix forms as byCode(). Treasures with appears = 0 are skipped.
   * Excluded players never match, so a player isn't reported as stealing
   * from himself. $secretKey false disables matching, returns false.
   *
   * @param string|false $secretKey game secret, or false to disable
   * @param string $string raw code the player submitted
   * @param int[] $excludePlayerIds players to exclude from matching
   * @param float $timeout seconds before the lookup is aborted, 0 for no limit
   * @return array|false treasure_id, player_id, encryptedCode, treasure_points, or false
   * @throws \yii\db\Exception with errorInfo[1] 1969 when $timeout is exceeded
   */
  public static function findByEncryptedCode($secretKey, $string, array $excludePlayerIds = [], $timeout = 0)
  {
    if ($secretKey === false || !preg_match(TreasureQuery::formRegex(), $string))
      return false;

    $hash = 'md5(HEX(AES_ENCRYPT(CONCAT(treasure.code, player.id), :secretKey)))';

    $query = (new \yii\db\Query())
      ->select([
        'treasure_id'     => 'treasure.id',
        'player_id'       => 'player.id',
        'encryptedCode'   => new \yii\db\Expression($hash),
        'treasure_points' => 'treasure.points',
      ])
      ->from(['treasure', 'player'])
      ->where(new \yii\db\Expression('treasure.appears != 0'))
      ->andWhere(TreasureQuery::anyForm($hash, $string, [':secretKey' => $secretKey]))
      ->addParams([':secretKey' => $secretKey]);

    if ($excludePlayerIds) {
      $query->andWhere(['not in', 'player.id', $excludePlayerIds]);
    }

    $command = $query->limit(1)->createCommand();
    if ($timeout > 0) {
      $params = $command->params;
      $command->setSql(sprintf('SET STATEMENT max_statement_time=%.3F FOR %s', $timeout, $command->getSql()))->bindValues($params);
    }

    return $command->queryOne();
  }

  /**
   * Handles a failed flag claim: penalizes every player involved and writes
   * the abuse log.
   *
   * Unknown flag penalizes nobody. Known flag penalizes the taker, plus the
   * owner who leaked it. Abuse rows are linked, the giver row points at the
   * taker row via model_id. Showing the result to the player is up to the
   * caller.
   *
   * @param Player $player the player who submitted the flag
   * @param string $string raw code the player submitted
   * @return string ClaimService::UNKNOWN or ClaimService::OWNED_BY_OTHER
   */
  public static function handleFailedClaim(Player $player, string $string): string
  {
    $playerId = $player->id;
    $pending = false;
    try {
      $owner = \Yii::$app->cache->memcache->add('failed_claim_lookup:' . $playerId, 1, 2)
        ? self::findByEncryptedCode(\Yii::$app->sys->treasure_secret_key, $string, [$playerId], (float) (\Yii::$app->sys->failed_claim_lookup_timeout ?: 1))
        : false;
    } catch (\yii\db\Exception $e) {
      if ((int) ($e->errorInfo[1] ?? 0) !== 1969) {
        throw $e;
      }
      $owner = false;
      $pending = true;
    }
    \Yii::$app->counters->increment('failed_claims');

    if ($owner === false) {
      $status = ClaimService::UNKNOWN;

      $overAllowance = \Yii::$app->sys->free_fail_allowance !== false
        && $player->profile->metric('failed_claims') > \Yii::$app->sys->free_fail_allowance;
      $actors = [[
        'player_id' => $playerId,
        'reason'    => $pending ? self::REASON_PENDING : 'failed_claim',
        'points'    => $overAllowance ? -\Yii::$app->sys->penalty_per_fail : 0,
        'resolved'  => $overAllowance ? 1 : 0,
        'penalty'   => $overAllowance ? \Yii::t('app', 'Too many failed flag claims') : null,
      ]];
    } else {
      $status = ClaimService::OWNED_BY_OTHER;

      $penaltyPoints = empty($owner['treasure_points'])
        ? self::PENALTY_FALLBACK_POINTS
        : -abs((int) $owner['treasure_points']);

      $actors = [
        [
          'player_id' => $playerId,
          'reason'    => 'claim_other_team',
          'points'    => $penaltyPoints,
          'resolved'  => 1,
          'penalty'   => \Yii::t('app', 'Tried to claim a flag that belongs to another team'),
        ],
        [
          'player_id' => $owner['player_id'],
          'reason'    => 'flag_shared',
          'points'    => $penaltyPoints,
          'resolved'  => 1,
          'penalty'   => \Yii::t('app', 'Shared a flag with another team'),
        ],
      ];
    }

    if (\Yii::$app->sys->apply_abuse_penalties === false) {
      foreach ($actors as &$actor) {
        $actor['points'] = 0;
        $actor['resolved'] = 0;
      }
      unset($actor);
    } else {
      foreach ($actors as $actor) {
        if ($actor['penalty'] !== null) {
          self::penalize($actor['player_id'], $actor['points'], $actor['penalty']);
        }
      }
    }


    if (!\Yii::$app->sys->log_failed_claims) {
      return $status;
    }

    $parentId = 0;
    foreach ($actors as $actor) {
      try {
        $parentId = self::logAbuse([
          'player_id' => $actor['player_id'],
          'title'     => $string,
          'reason'    => $actor['reason'],
          'model'     => 'failed_claim',
          'model_id'  => $parentId,
          'points'    => $actor['points'],
          'resolved'  => $actor['resolved'],
        ]);
      } catch (\Exception $e) {
        $parentId = 0;
      }
    }

    return $status;
  }

  /**
   * Inserts a penalty entry in the stream for a single player.
   *
   * Used for flag abuse: called once for the taker, once for the giver.
   *
   * @param int $playerId player receiving the penalty
   * @param int $points penalty amount, negative
   * @param string $title already translated text, reused for message/pubtitle/pubmessage
   * @return int number of rows affected
   * @throws \yii\db\Exception on insert failure
   */
  public static function penalize($playerId, $points, $title)
  {
    return \Yii::$app->db->createCommand()->insert('stream', [
      'player_id'  => $playerId,
      'model'      => 'abuse',
      'model_id'   => new \yii\db\Expression('UNIX_TIMESTAMP()'),
      'points'     => $points,
      'title'      => $title,
      'message'    => $title,
      'pubtitle'   => $title,
      'pubmessage' => $title,
    ])->execute();
  }

  /**
   * Inserts an abuse log entry for a single player.
   *
   * Caller supplies player_id, title, reason, model, points, resolved.
   * Missing columns (model_id, timestamps) are filled here.
   *
   * @param array $row column name => value pairs for the abuser table
   * @return int id of the inserted row
   * @throws \yii\db\Exception on insert failure
   */
  public static function logAbuse(array $row)
  {
    \Yii::$app->db->createCommand()->insert('abuser', $row + [
      'model_id'   => 0,
      'created_at' => new \yii\db\Expression('NOW()'),
      'updated_at' => new \yii\db\Expression('NOW()'),
    ])->execute();

    return (int) \Yii::$app->db->getLastInsertID();
  }
}
