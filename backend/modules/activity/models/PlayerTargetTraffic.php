<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;

/**
 * This is the model class for table "player_target_traffic".
 * Filled by the traffic/collect console command from pflow exports.
 *
 * @property int $player_id
 * @property int $target_id
 * @property int $packets_sent player to target
 * @property int $packets_received target to player
 * @property int $bytes_sent player to target
 * @property int $bytes_received target to player
 * @property string $first_seen
 * @property string $last_seen
 *
 * @property Player $player
 * @property Target $target
 */
class PlayerTargetTraffic extends \yii\db\ActiveRecord
{
  public static function tableName()
  {
    return 'player_target_traffic';
  }

  public static function primaryKey()
  {
    return ['player_id', 'target_id'];
  }

  public function rules()
  {
    return [
      [['player_id', 'target_id'], 'required'],
      [['player_id', 'target_id', 'packets_sent', 'packets_received', 'bytes_sent', 'bytes_received'], 'integer'],
      [['first_seen', 'last_seen'], 'safe'],
    ];
  }

  public function attributeLabels()
  {
    return [
      'player_id' => Yii::t('app', 'Player ID'),
      'target_id' => Yii::t('app', 'Target ID'),
      'packets_sent' => Yii::t('app', 'Packets Sent'),
      'packets_received' => Yii::t('app', 'Packets Received'),
      'bytes_sent' => Yii::t('app', 'Bytes Sent'),
      'bytes_received' => Yii::t('app', 'Bytes Received'),
      'first_seen' => Yii::t('app', 'First Seen'),
      'last_seen' => Yii::t('app', 'Last Seen'),
    ];
  }

  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }

  public function getTarget()
  {
    return $this->hasOne(Target::class, ['id' => 'target_id']);
  }
}
