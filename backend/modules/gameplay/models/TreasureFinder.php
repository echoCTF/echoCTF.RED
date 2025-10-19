<?php
namespace app\modules\gameplay\models;

class TreasureFinder extends \yii\base\Model
{
    public static function findByEncryptedCode($secretKey, $string)
    {
        return \Yii::$app->db->createCommand("
            SELECT treasure.id as treasure_id, player.id AS player_id
            FROM treasure, player
            WHERE md5(HEX(AES_ENCRYPT(CONCAT(code, player.id), :secretKey))) LIKE :code
        ", [
            ':secretKey' => $secretKey,
            ':code' => $string,
        ])->queryOne();
    }
}