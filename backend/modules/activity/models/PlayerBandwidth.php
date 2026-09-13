<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
/**
 * This is the model class for table "player_bandwidth".
 *
 * @property int $id
 * @property int $player_id
 * @property int|null $vpn_local_address
 * @property int $bytes_received
 * @property int $bytes_sent
 * @property int $duration
 * @property string $ts
 *
 * @property Player $player
 */
class PlayerBandwidth extends \yii\db\ActiveRecord
{

  public $vpn_local_address_octet;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_bandwidth';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['vpn_local_address'], 'default', 'value' => null],
      [['duration'], 'default', 'value' => 0],
      [['player_id'], 'required'],
      [['player_id', 'vpn_local_address', 'bytes_received', 'bytes_sent', 'duration'], 'integer'],
      [['ts'], 'safe'],
      [['vpn_local_address_octet'], 'ip'],
      [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'player_id' => Yii::t('app', 'Player ID'),
      'vpn_local_address' => Yii::t('app', 'Vpn Local Address'),
      'vpn_local_address_octet' => Yii::t('app', 'Vpn Local Address'),
      'bytes_received' => Yii::t('app', 'Bytes Received'),
      'bytes_sent' => Yii::t('app', 'Bytes Sent'),
      'duration' => Yii::t('app', 'Duration'),
      'ts' => Yii::t('app', 'Ts'),
    ];
  }

  public function afterFind()
  {
    parent::afterFind();
    $this->vpn_local_address_octet = long2ip($this->vpn_local_address);
  }

  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      $this->vpn_local_address = ip2long($this->vpn_local_address_octet);
      return true;
    } else {
      return false;
    }
  }

  /**
   * Gets query for [[Player]].
   *
   * @return \yii\db\ActiveQuery|PlayerQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }

  /**
   * {@inheritdoc}
   * @return PlayerBandwidthQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerBandwidthQuery(get_called_class());
  }
}
