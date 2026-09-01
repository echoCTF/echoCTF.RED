<?php

namespace app\modules\target\models;

class FailedClaimHandler extends \yii\base\Model
{
  /** Used when the matched treasure has no points set. */
  const PENALTY_FALLBACK_POINTS = -1500;

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
   * @return array|false treasure_id, player_id, encryptedCode, treasure_points, or false
   */
  public static function findByEncryptedCode($secretKey=false, $string, array $excludePlayerIds = [])
  {
    if ($secretKey === false)
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
      ->andWhere([
        'or',
        new \yii\db\Expression("$hash = :code"),
        new \yii\db\Expression("CONCAT('ETSCTF_', $hash) = :code"),
        new \yii\db\Expression("CONCAT('ETSCTF:', $hash) = :code"),
        new \yii\db\Expression("CONCAT('ETSCTF ', $hash) = :code"),
        new \yii\db\Expression("CONCAT('ETSCTF.', $hash) = :code"),
        new \yii\db\Expression("CONCAT('ETSCTF-', $hash) = :code"),
        new \yii\db\Expression("CONCAT('ETSCTF{', $hash, '}') = :code"),
      ])
      ->addParams([':secretKey' => $secretKey, ':code' => $string]);

    if ($excludePlayerIds) {
      $query->andWhere(['not in', 'player.id', $excludePlayerIds]);
    }

    return $query->limit(1)->one();
  }

  /**
   * Handles a failed flag claim: flashes the reason, penalizes every player
   * involved and writes the abuse log.
   *
   * Unknown flag penalizes nobody. Known flag penalizes the taker, plus the
   * owner who leaked it. Abuse rows are linked, the giver row points at the
   * taker row via model_id.
   *
   * @param string $string raw code the player submitted
   * @return void
   */
  public static function handleFailedClaim($string)
  {
    $playerId = \Yii::$app->user->id;
    $owner = self::findByEncryptedCode(\Yii::$app->sys->treasure_secret_key, $string, [$playerId]);
    $flag = \yii\helpers\Html::encode($string);

    if ($owner === false) {
      \Yii::$app->session->addFlash('error', \Yii::t('app', 'Flag [<strong>{flag}</strong>] does not exist!', ['flag' => $flag]));

      $actors = [[
        'player_id' => $playerId,
        'reason'    => 'failed_claim',
        'points'    => 0,
        'resolved'  => 0,
        'penalty'   => null,
      ]];
    } else {
      \Yii::$app->session->addFlash('error', \Yii::t('app', 'Flag [<strong>{flag}</strong>] belongs to someone else you will get penalized!', ['flag' => $flag]));

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

    foreach ($actors as $actor) {
      if ($actor['penalty'] !== null) {
        self::penalize($actor['player_id'], $actor['points'], $actor['penalty']);
      }
    }

    \Yii::$app->counters->increment('failed_claims');

    if (!\Yii::$app->sys->log_failed_claims) {
      return;
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
