<?php

namespace app\modules\moderation\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use app\components\validators\ExtendedIpValidator;

/**
 * This is the model class for table "shared_network".
 *
 * Stores IPv4 ranges of shared networks (universities, NAT pools, etc.) that
 * produce legitimate duplicate logins and can be excluded from duplicate-IP
 * checks through {@link SharedNetwork::excludeShared()}.
 *
 * @property int $id
 * @property int $network base network address, stored normalized (network & mask)
 * @property int $mask netmask
 * @property string|null $description
 * @property string|null $created_at
 */
class SharedNetwork extends \yii\db\ActiveRecord
{
  /**
   * @var string CIDR input (eg. "10.0.0.0/6" or "255.192.0.0" netmask style)
   */
  public $cidr;

  /**
   * @var boolean when true, existing entries swallowed by the new network are removed
   */
  public $absorb = false;

  public function behaviors()
  {
    return [
      [
        'class' => TimestampBehavior::class,
        'value' => new \yii\db\Expression('NOW()'),
      ],
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'attributeTypes' => [
          'id' => AttributeTypecastBehavior::TYPE_INTEGER,
          'network' => AttributeTypecastBehavior::TYPE_INTEGER,
          'mask' => AttributeTypecastBehavior::TYPE_INTEGER,
        ],
        'typecastAfterValidate' => true,
        'typecastBeforeSave' => true,
        'typecastAfterFind' => true,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'shared_network';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['description'], 'trim'],
      [['description'], 'string', 'max' => 255],
      [['cidr'], 'required', 'when' => function ($model) {
        return $model->isNewRecord;
      }],
      [['cidr'], 'parseCidr'],
      [['cidr'], 'checkContainment', 'skipOnError' => true],
      [['absorb'], 'boolean'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'cidr' => Yii::t('app', 'Network (CIDR)'),
      'network' => Yii::t('app', 'Network'),
      'mask' => Yii::t('app', 'Netmask'),
      'description' => Yii::t('app', 'Description'),
      'created_at' => Yii::t('app', 'Created'),
    ];
  }

  /**
   * Parses the CIDR input into the normalized network and mask attributes.
   * Only meaningful on new records, updates change the description only.
   */
  public function parseCidr($attribute, $params)
  {
    if (!$this->isNewRecord || empty($this->cidr))
      return;

    $value = trim($this->cidr);
    $validator = new ExtendedIpValidator(['subnet' => true, 'expandIPv6' => false]);
    // Validator::validate() returns true when valid, false when invalid
    if ($validator->validate($value) !== true || filter_var(explode('/', $value, 2)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false)
    {
      $this->addError($attribute, Yii::t('app', 'Invalid IPv4 network, use eg. 10.0.0.0/8 or 172.16.0.0/255.240.0.0.'));
      return;
    }

    if (strpos($value, '/') !== false)
      [$ip, $maskPart] = explode('/', $value, 2);
    else
      [$ip, $maskPart] = [$value, '32'];

    $ipLong = (int) sprintf('%u', ip2long($ip));
    if (filter_var($maskPart, FILTER_VALIDATE_IP))
    {
      $netmaskLong = (int) sprintf('%u', ip2long($maskPart));
    }
    else
    {
      $prefix = (int) $maskPart;
      if ($prefix < 0 || $prefix > 32)
      {
        $this->addError($attribute, Yii::t('app', 'Invalid network prefix, allowed range is 0-32.'));
        return;
      }
      $netmaskLong = (~((1 << (32 - $prefix)) - 1)) & 0xFFFFFFFF;
    }

    $this->network = $ipLong & $netmaskLong;
    $this->mask = $netmaskLong;
  }

  /**
   * Containment validation on top of the database unique index:
   * - rejects networks already covered by an existing bigger one
   *   (eg. adding 10.10.0.0/24 when 10.0.0.0/6 exists is pointless),
   * - rejects networks swallowing existing smaller ones, unless the
   *   absorb flag is set (then beforeSave removes the covered entries).
   */
  public function checkContainment($attribute, $params)
  {
    if (!$this->isNewRecord || $this->network === null || $this->mask === null)
      return;

    $coveredBy = self::find()
      ->andWhere(['<=', 'mask', $this->mask])
      ->andWhere(new Expression('(:network & mask) = network', [':network' => $this->network]))
      ->all();
    foreach ($coveredBy as $sn)
    {
      $this->addError($attribute, Yii::t('app', 'Network already covered by {cidr}{comment}', [
        'cidr' => $sn->cidr,
        'comment' => $sn->description === null ? '' : ' (' . $sn->description . ')',
      ]));
      return;
    }

    if ($this->absorb)
      return;

    $covers = self::find()
      ->andWhere(['>=', 'mask', $this->mask])
      ->andWhere(new Expression('(network & :mask) = :network', [':mask' => $this->mask, ':network' => $this->network]))
      ->all();
    foreach ($covers as $sn)
    {
      $this->addError($attribute, Yii::t('app', 'Network swallows existing {cidr}{comment}, use absorb to remove covered networks', [
        'cidr' => $sn->cidr,
        'comment' => $sn->description === null ? '' : ' (' . $sn->description . ')',
      ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function beforeSave($insert)
  {
    if (!parent::beforeSave($insert))
      return false;

    if ($insert && $this->absorb)
    {
      $swallowedIds = self::find()->select('id')
        ->andWhere(['>=', 'mask', $this->mask])
        ->andWhere(new Expression('(network & :mask) = :network', [':mask' => $this->mask, ':network' => $this->network]))
        ->column();
      if (!empty($swallowedIds))
        self::deleteAll(['id' => $swallowedIds]);
    }

    return true;
  }

  /**
   * {@inheritdoc}
   * Repopulates the cidr input attribute from the stored network/mask:
   * the public $cidr property shadows the computed getter for property
   * access, so records loaded from the database must set it explicitly.
   */
  public function afterFind()
  {
    parent::afterFind();
    $this->cidr = long2ip((int) $this->network) . '/' . self::prefixFromMask((int) $this->mask);
  }

  /**
   * Prefix length for an IPv4 netmask stored as an unsigned integer.
   */
  public static function prefixFromMask($mask)
  {
    return strlen(rtrim(decbin((int) $mask & 0xFFFFFFFF), '0'));
  }

  /**
   * Finds an entry by CIDR string or numeric id.
   */
  public static function findByCidr($value)
  {
    if (filter_var($value, FILTER_VALIDATE_INT) !== false)
      return self::findOne((int) $value);

    $tmp = new self();
    $tmp->cidr = $value;
    $tmp->parseCidr('cidr', null);
    if ($tmp->hasErrors())
      return null;
    return self::findOne(['network' => $tmp->network, 'mask' => $tmp->mask]);
  }

  /**
   * Adds a NOT EXISTS condition to $query excluding rows whose IP falls
   * inside any shared network.
   *
   * @param \yii\db\ActiveQuery $query the query to modify
   * @param string $ipExpression raw SQL expression of an unsigned int IP column
   * (eg. 'player_last.signup_ip')
   */
  public static function excludeShared($query, $ipExpression)
  {
    $subQuery = (new \yii\db\Query())
      ->from('shared_network')
      ->where(new Expression("({$ipExpression} & shared_network.mask) = shared_network.network"));
    $query->andFilterWhere(['not exists', $subQuery]);
  }
}
