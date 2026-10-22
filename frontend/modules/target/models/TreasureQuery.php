<?php

namespace app\modules\target\models;

use Yii;
use yii\db\Expression;

class TreasureQuery extends \yii\db\ActiveQuery
{
  const FORMS = ['%s', 'ETSCTF_%s', 'ETSCTF:%s', 'ETSCTF %s', 'ETSCTF.%s', 'ETSCTF-%s', 'ETSCTF{%s}'];

  /**
   * Builds the OR condition matching $code against $expr in every accepted spelling.
   * %s in FORMS marks where $expr goes.
   */
  public static function anyForm(string $expr, string $code, array $params = []): array
  {
    $or = ['or'];
    foreach (self::FORMS as $form) {
      [$pre, $post] = explode('%s', $form, 2);
      $args = [];
      if ($pre !== '') $args[] = "'$pre'";
      $args[] = $expr;
      if ($post !== '') $args[] = "'$post'";
      $sql = count($args) > 1 ? 'CONCAT(' . implode(', ', $args) . ')' : $expr;
      $or[] = new Expression("$sql = :code", $params + [':code' => $code]);
    }
    return $or;
  }

  public function forTarget($id)
  {
    return $this->andWhere(['target_id' => $id]);
  }

  public function byCode($code)
  {
    return $this->andWhere(self::anyForm('code', $code));
  }

  public function byEncryptedCode($code, $player_id = false, $secretKey = false)
  {
    if ($secretKey == false && Yii::$app->sys->treasure_secret_key !== false)
      $secretKey = Yii::$app->sys->treasure_secret_key;

    if ($player_id === false) $player_id = intval(Yii::$app->user->id);

    if ($secretKey !== false && $player_id !== false) {
      $hash = 'md5(HEX(AES_ENCRYPT(CONCAT(code, :player_id), :secretKey)))';
      return $this->andWhere(self::anyForm($hash, $code, [':player_id' => $player_id, ':secretKey' => $secretKey]));
    }
  }

  public function byTeamEncryptedCode($code, $team_id = false, $secretKey = false)
  {
    if ($secretKey == false && Yii::$app->sys->treasure_secret_key !== false) {
      $secretKey = Yii::$app->sys->treasure_secret_key;
    }

    if ($team_id === false || $secretKey === false) {
      return $this;
    }

    // Join approved team players
    $this->innerJoin(
      'team_player tp',
      'tp.team_id = :team_id AND tp.approved = 1',
      [':team_id' => $team_id]
    );

    $hash = 'md5(HEX(AES_ENCRYPT(CONCAT(treasure.code, tp.player_id), :secretKey)))';
    return $this->andWhere(self::anyForm($hash, $code, [':secretKey' => $secretKey]));
  }
  public function claimable()
  {
    return $this->andWhere(new \yii\db\Expression('appears!=0'));
  }

  public function notBy(int $player_id)
  {
    return $this->andWhere(new \yii\db\Expression('treasure.id NOT IN (SELECT treasure_id FROM player_treasure WHERE player_id=:player_id)', [':player_id' => $player_id]));
  }
}
