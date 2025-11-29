<?php

namespace app\modules\infrastructure\models;

use app\modules\gameplay\models\Target;
use app\modules\infrastructure\models\Server;

use Yii;

/**
 * This is the model class for table "private_network_target".
 *
 * @property int $id
 * @property int|null $private_network_id
 * @property int|null $target_id
 * @property int|null $ip
 * @property string|null $ipoctet
 *
 * @property PrivateNetwork $privateNetwork
 * @property Target $target
 * @property Server $server
 */
class PrivateNetworkTarget extends \yii\db\ActiveRecord
{


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'private_network_target';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['private_network_id', 'target_id', 'ip', 'server_id'], 'default', 'value' => null],
      [['state'], 'default', 'value' => 0],
      [['private_network_id', 'target_id', 'ip', 'server_id','state'], 'integer'],
      ['ipoctet', 'ip'],
      [['private_network_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrivateNetwork::class, 'targetAttribute' => ['private_network_id' => 'id']],
      [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
      [['server_id'], 'exist', 'skipOnError' => true, 'targetClass' => Server::class, 'targetAttribute' => ['server_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'private_network_id' => Yii::t('app', 'Private Network'),
      'private_network_name' => Yii::t('app', 'Private Network'),
      'target_id' => Yii::t('app', 'Target'),
      'target_name' => Yii::t('app', 'Target'),
      'server_name' => Yii::t('app', 'Server'),
      'ip' => Yii::t('app', 'IP (integer)'),
      'ipoctet' => Yii::t('app', 'IP'),
    ];
  }

  /**
   * Gets query for [[PrivateNetwork]].
   *
   * @return \yii\db\ActiveQuery|PrivateNetworkQuery
   */
  public function getPrivateNetwork()
  {
    return $this->hasOne(PrivateNetwork::class, ['id' => 'private_network_id']);
  }

  /**
   * Gets query for [[Target]].
   *
   * @return \yii\db\ActiveQuery|TargetQuery
   */
  public function getTarget()
  {
    return $this->hasOne(Target::class, ['id' => 'target_id']);
  }

  /**
   * Gets query for [[Server]].
   *
   * @return \yii\db\ActiveQuery|TargetQuery
   */
  public function getServer()
  {
    return $this->hasOne(Target::class, ['id' => 'server_id']);
  }

  /**
   * {@inheritdoc}
   * @return PrivateNetworkTargetQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PrivateNetworkTargetQuery(get_called_class());
  }

  public function beforeValidate()
  {
    if ($this->ipoctet) {
      $this->ip = ip2long($this->ipoctet);
    } else
      $this->ip = null;

    return parent::beforeValidate();
  }

  public function beforeSave($insert)
  {
    unset($this->ipoctet);
    return parent::beforeSave($insert);
  }
}
