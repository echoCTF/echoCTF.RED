<?php

namespace app\modules\target\models;

use Yii;
use app\models\Player;
use app\models\PlayerTreasure;
use app\modules\target\models\TargetPlayerState as TPS;

/**
 * The flag claim, shared by the web form and the API.
 *
 * It returns a status. It never touches the session, a view or a response
 * code, the caller turns the status into a flash or a JSON answer.
 */
class ClaimService
{
  const CLAIMED = 'claimed';
  const CLAIMED_BEFORE = 'claimed_before';
  const UNKNOWN = 'unknown';
  const OWNED_BY_OTHER = 'owned_by_other';
  const NO_FINDING = 'no_finding';
  const NO_NETWORK = 'no_network';
  const NO_CLAIMS_LEFT = 'no_claims_left';

  /**
   * Claims a flag for a player.
   *
   * Looks the code up as a static flag, then as a personal (team or player)
   * encrypted flag. A code that matches a used up flag (appears = 0) is
   * NO_CLAIMS_LEFT. A code that matches nothing goes to
   * FailedClaimHandler::handleFailedClaim().
   *
   * @param string $code raw code the player submitted, trimmed here
   * @param Player $player the claiming player
   * @return array status (a ClaimService constant), treasure (Treasure|null),
   *   points (int|null, only for CLAIMED), message (string|null, only for NO_NETWORK)
   * @throws \Throwable when saving the claim fails
   */
  public static function claim(string $code, Player $player): array
  {
    $code = trim($code);
    $treasure = self::lookup($code, $player, false);

    if ($treasure === null) {
      $used = self::lookup($code, $player, true);
      if ($used !== null) {
        return self::result(self::NO_CLAIMS_LEFT, $used);
      }
      // Checks if the flag belongs to another player and produces the penalties and abuser records
      return self::result(FailedClaimHandler::handleFailedClaim($player, $code));
    }

    if (PlayerTreasure::findOne(['player_id' => (int) $player->id, 'treasure_id' => $treasure->id]) !== null) {
      return self::result(self::CLAIMED_BEFORE, $treasure);
    }

    $progress = TPS::find()->where(['id' => $treasure->target_id, 'player_id' => $player->id])->exists();
    if ((Yii::$app->sys->force_findings_to_claim || $treasure->target->require_findings) && $progress === false && intval($treasure->target->getFindings()->count()) > 0) {
      Yii::$app->counters->increment('claim_no_finding');
      return self::result(self::NO_FINDING, $treasure);
    }

    try {
      Yii::$app->getModule('target')->checkNetwork($treasure->target);
    } catch (\Throwable $e) {
      return self::result(self::NO_NETWORK, $treasure, null, $e->getMessage());
    }

    Yii::$app->counters->increment('claims');
    return self::save($treasure, $player);
  }

  /**
   * Finds the treasure a code points to, as a static flag, then as a personal
   * (team or player) encrypted flag.
   *
   * @param string $code trimmed code the player submitted
   * @param Player $player the claiming player
   * @param bool $exhausted false for flags with claims left, true for used up flags (appears = 0)
   * @return Treasure|null
   */
  private static function lookup(string $code, Player $player, bool $exhausted): ?Treasure
  {
    $find = fn() => $exhausted ? Treasure::find()->exhausted() : Treasure::find()->claimable();
    $treasure = $find()->byCode($code)->one();

    if ($treasure === null && Yii::$app->sys->treasure_secret_key !== false) {
      if (Yii::$app->sys->team_encrypted_claims_allowed === true && $player->teamPlayer && $player->teamPlayer->approved) {
        $treasure = $find()->byTeamEncryptedCode($code, $player->teamPlayer->team_id)->one();
      } else {
        $treasure = $find()->byEncryptedCode($code, $player->id)->one();
      }
    }
    return $treasure;
  }

  /**
   * Inserts the PlayerTreasure row and takes one claim off the treasure.
   *
   * @param Treasure $treasure the treasure being claimed
   * @param Player $player the claiming player
   * @return array result() with CLAIMED, CLAIMED_BEFORE or NO_CLAIMS_LEFT
   * @throws \Throwable on any other failure, after the transaction is rolled back
   */
  private static function save(Treasure $treasure, Player $player): array
  {
    $transaction = Yii::$app->db->beginTransaction();
    try {
      $PT = new PlayerTreasure();
      $PT->player_id = (int) $player->id;
      $PT->treasure_id = $treasure->id;
      try {
        $saved = $PT->save();
      } catch (\yii\db\IntegrityException $e) {
        $transaction->rollBack();
        return self::result(self::CLAIMED_BEFORE, $treasure);
      }
      if ($saved && $treasure->appears !== -1 && Treasure::updateAllCounters(['appears' => -1], ['and', ['id' => $treasure->id], ['>', 'appears', 0]]) === 0) {
        $transaction->rollBack();
        return self::result(self::NO_CLAIMS_LEFT, $treasure);
      }
      $transaction->commit();
      self::heartbeat($treasure->target);
      $PT->refresh();
      return self::result(self::CLAIMED, $treasure, (int) $PT->points);
    } catch (\Throwable $e) {
      $transaction->rollBack();
      throw $e;
    }
  }

  /**
   * Touches the heartbeat of a running on-demand target so it is not shut down.
   *
   * @param Target $target the target of the claimed treasure
   * @return void
   */
  private static function heartbeat($target): void
  {
    if ($target->ondemand && $target->ondemand->state > 0) {
      $target->ondemand->updateAttributes(['heartbeat' => new \yii\db\Expression('NOW()')]);
    }
  }

  private static function result(string $status, ?Treasure $treasure = null, ?int $points = null, ?string $message = null): array
  {
    return ['status' => $status, 'treasure' => $treasure, 'points' => $points, 'message' => $message];
  }
}
