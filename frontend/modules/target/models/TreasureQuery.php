<?php

namespace app\modules\target\models;

use Yii;
use yii\db\Expression;

class TreasureQuery extends \yii\db\ActiveQuery
{
  public function forTarget($id)
  {
    return $this->andWhere(['target_id' => $id]);
  }

  public function active()
  {
    return $this->andWhere(['active' => 1]);
  }

  public function byCode($code)
  {
    return $this->andWhere([
      'or',
      ['code' => $code],
      ['CONCAT("ETSCTF_",code)' => $code],
      ['CONCAT("ETSCTF:",code)' => $code],
      ['CONCAT("ETSCTF ",code)' => $code],
      ['CONCAT("ETSCTF.",code)' => $code],
      ['CONCAT("ETSCTF-",code)' => $code],
      ['CONCAT("ETSCTF{",code,"}")' => $code],
    ]);
  }

  public function byEncryptedCode($code, $player_id = false, $secretKey = false)
  {
    if ($secretKey == false && Yii::$app->sys->treasure_secret_key !== false)
      $secretKey = Yii::$app->sys->treasure_secret_key;

    if ($player_id === false) $player_id = intval(Yii::$app->user->id);

    if ($secretKey !== false && $player_id !== false)
      return $this->andWhere([
        'or',
        new Expression(
          'md5(HEX(AES_ENCRYPT(CONCAT(code, :player_id), :secretKey))) = :code',
          [':player_id' => $player_id, ':secretKey' => $secretKey, ':code' => $code]
        ),
        new Expression(
          'CONCAT("ETSCTF_", md5(HEX(AES_ENCRYPT(CONCAT(code, :player_id), :secretKey)))) = :code',
          [':player_id' => $player_id, ':secretKey' => $secretKey, ':code' => $code]
        ),
        new Expression(
          'CONCAT("ETSCTF:", md5(HEX(AES_ENCRYPT(CONCAT(code, :player_id), :secretKey)))) = :code',
          [':player_id' => $player_id, ':secretKey' => $secretKey, ':code' => $code]
        ),
        new Expression(
          'CONCAT("ETSCTF ", md5(HEX(AES_ENCRYPT(CONCAT(code, :player_id), :secretKey)))) = :code',
          [':player_id' => $player_id, ':secretKey' => $secretKey, ':code' => $code]
        ),
        new Expression(
          'CONCAT("ETSCTF.", md5(HEX(AES_ENCRYPT(CONCAT(code, :player_id), :secretKey)))) = :code',
          [':player_id' => $player_id, ':secretKey' => $secretKey, ':code' => $code]
        ),
        new Expression(
          'CONCAT("ETSCTF-", md5(HEX(AES_ENCRYPT(CONCAT(code, :player_id), :secretKey)))) = :code',
          [':player_id' => $player_id, ':secretKey' => $secretKey, ':code' => $code]
        ),
        new Expression(
          'CONCAT("ETSCTF{", md5(HEX(AES_ENCRYPT(CONCAT(code, :player_id), :secretKey))), "}") = :code',
          [':player_id' => $player_id, ':secretKey' => $secretKey, ':code' => $code]
        ),
      ]);
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

    return $this->andWhere([
      'or',
      new Expression(
        'md5(HEX(AES_ENCRYPT(CONCAT(treasure.code, tp.player_id), :secretKey))) = :code',
        [':secretKey' => $secretKey, ':code' => $code]
      ),
      new Expression(
        'CONCAT("ETSCTF_", md5(HEX(AES_ENCRYPT(CONCAT(treasure.code, tp.player_id), :secretKey)))) = :code',
        [':secretKey' => $secretKey, ':code' => $code]
      ),
      new Expression(
        'CONCAT("ETSCTF:", md5(HEX(AES_ENCRYPT(CONCAT(treasure.code, tp.player_id), :secretKey)))) = :code',
        [':secretKey' => $secretKey, ':code' => $code]
      ),
      new Expression(
        'CONCAT("ETSCTF ", md5(HEX(AES_ENCRYPT(CONCAT(treasure.code, tp.player_id), :secretKey)))) = :code',
        [':secretKey' => $secretKey, ':code' => $code]
      ),
      new Expression(
        'CONCAT("ETSCTF.", md5(HEX(AES_ENCRYPT(CONCAT(treasure.code, tp.player_id), :secretKey)))) = :code',
        [':secretKey' => $secretKey, ':code' => $code]
      ),
      new Expression(
        'CONCAT("ETSCTF-", md5(HEX(AES_ENCRYPT(CONCAT(treasure.code, tp.player_id), :secretKey)))) = :code',
        [':secretKey' => $secretKey, ':code' => $code]
      ),
      new Expression(
        'CONCAT("ETSCTF{", md5(HEX(AES_ENCRYPT(CONCAT(treasure.code, tp.player_id), :secretKey))), "}") = :code',
        [':secretKey' => $secretKey, ':code' => $code]
      ),
    ]);
  }
  public function claimable()
  {
    return $this->andWhere(new \yii\db\Expression('appears!=0'));
  }

  public function notBy(int $player_id)
  {
    return $this->andWhere(new \yii\db\Expression('treasure.id NOT IN (SELECT treasure_id FROM player_treasure WHERE player_id=' . $player_id . ')'));
  }
}
