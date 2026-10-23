<?php

namespace app\modules\settings\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "vpn_ip_whitelist".
 *
 * @property int $id
 * @property int $ip
 * @property int $mask
 * @property string|null $description
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property string $network IP address, IP/prefix or IP/netmask as entered on the form
 * @property string $ipoctet
 * @property string $maskoctet
 * @property int $prefix
 */
class VpnIpWhitelist extends \yii\db\ActiveRecord
{
  public $network;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'vpn_ip_whitelist';
  }

  public function behaviors()
  {
    return [
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'typecastAfterValidate' => false,
        'typecastBeforeSave' => true,
        'typecastAfterFind' => true,
      ],
      [
        'class' => TimestampBehavior::class,
        'createdAtAttribute' => 'created_at',
        'updatedAtAttribute' => 'updated_at',
        'value' => new Expression('NOW()'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['network'], 'required'],
      [['network'], 'filter', 'filter' => 'trim'],
      [['network'], 'validateNetwork'],
      [['description'], 'string'],
      [['created_at', 'updated_at'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'ip' => Yii::t('app', 'IP'),
      'mask' => Yii::t('app', 'Mask'),
      'network' => Yii::t('app', 'Network'),
      'ipoctet' => Yii::t('app', 'IP'),
      'maskoctet' => Yii::t('app', 'Netmask'),
      'prefix' => Yii::t('app', 'Prefix'),
      'description' => Yii::t('app', 'Description'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
    ];
  }

  /**
   * {@inheritdoc}
   * @return VpnIpWhitelistQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new VpnIpWhitelistQuery(get_called_class());
  }

  /**
   * Splits the network input into an ip and a netmask, a bare IP is a single host.
   *
   * @return array|null [ip, mask] as integers, null if it cannot be parsed
   */
  public static function parseNetwork($network)
  {
    $parts = explode('/', trim((string)$network), 2);
    $ip = ip2long($parts[0]);
    if ($ip === false)
      return null;
    if (!isset($parts[1]))
      return [$ip, 0xFFFFFFFF];
    if (filter_var($parts[1], FILTER_VALIDATE_IP)) {
      $mask = ip2long($parts[1]);
    } elseif (ctype_digit($parts[1]) && (int)$parts[1] <= 32) {
      $mask = (~((1 << (32 - (int)$parts[1])) - 1)) & 0xFFFFFFFF;
    } else {
      return null;
    }
    return [$ip & $mask, $mask];
  }

  /**
   * Checks that the network is an IPv4 address, IP/prefix or IP/netmask and not the whole address space.
   *
   * @param string $attribute the attribute being validated
   * @return void
   */
  public function validateNetwork($attribute)
  {
    $validator = new \app\components\validators\ExtendedIpValidator(['subnet' => null, 'ipv6' => false]);
    if (!$validator->validate($this->$attribute, $error)) {
      $this->addError($attribute, $error);
      return;
    }
    $parsed = self::parseNetwork($this->$attribute);
    if ($parsed === null)
      $this->addError($attribute, Yii::t('app', 'Network is not a valid IP address, IP/prefix or IP/netmask.'));
    elseif ($parsed[1] === 0)
      $this->addError($attribute, Yii::t('app', 'The network must not match every address.'));
  }

  public function getIpoctet()
  {
    return long2ip($this->ip);
  }

  public function getMaskoctet()
  {
    return long2ip($this->mask);
  }

  public function getPrefix()
  {
    return substr_count(decbin($this->mask), '1');
  }

  public function afterFind()
  {
    parent::afterFind();
    $this->network = $this->ipoctet . '/' . $this->prefix;
  }

  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      [$this->ip, $this->mask] = self::parseNetwork($this->network);
      return true;
    }
    return false;
  }
}
