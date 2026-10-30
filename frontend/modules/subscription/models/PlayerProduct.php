<?php

namespace app\modules\subscription\models;

use Yii;
use app\models\Player;
use yii\base\UserException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "player_product".
 *
 * @property int $id
 * @property int $player_id
 * @property string $price_id
 * @property string|null $ending
 * @property string|null $metadata
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class PlayerProduct extends \yii\db\ActiveRecord
{


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_product';
  }

  public function behaviors()
  {
    return [
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
      [['ending', 'metadata', 'created_at', 'updated_at'], 'default', 'value' => null],
      [['player_id', 'price_id'], 'required'],
      [['player_id'], 'integer'],
      [['ending', 'metadata', 'created_at', 'updated_at'], 'safe'],
      [['price_id'], 'string', 'max' => 32],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'player_id' => 'Player ID',
      'price_id' => 'Price ID',
      'ending' => 'Ending',
      'metadata' => 'Metadata',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
    ];
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
   * Gets query for [[Product]].
   *
   * @return \yii\db\ActiveQuery|ProductQuery
   */
  public function getProduct()
  {
    return $this->hasOne(Product::class, ['id' => 'product_id']);
  }

  /**
   * Gets query for [[Price]].
   *
   * @return \yii\db\ActiveQuery|PriceQuery
   */
  public function getPrice()
  {
    return $this->hasOne(Price::class, ['id' => 'price_id']);
  }

  /**
   * Gets decoded json metadata for [[PlayerProduct]].
   *
   * @return StdClass
   */
  public function getMetadataObj()
  {
    return json_decode($this->metadata);
  }

  /**
   * {@inheritdoc}
   * @return PlayerSubscriptionQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerProductQuery(get_called_class());
  }

  /**
   * Number of target instances the product adds to the player while this
   * purchase lasts (`max_target_instances` product metadata key).
   */
  public function getMaxInstancesBonus(): int
  {
    $metadata = $this->product !== null ? json_decode((string) $this->product->metadata) : null;
    if (is_object($metadata) && isset($metadata->max_target_instances)) {
      return max(0, intval($metadata->max_target_instances));
    }
    return 0;
  }

  /** Add the product instances and networks when the purchase is created */
  public function afterSave($insert, $changedAttributes)
  {
    parent::afterSave($insert, $changedAttributes);
    if ($insert) {
      \app\models\PlayerSpin::adjustMaxInstances(intval($this->player_id), $this->maxInstancesBonus);
      $this->grantNetworks();
    }
  }

  /**
   * Give the player the networks of the product (`network_ids` product
   * metadata key). Only networks the player didn't already have are added,
   * and they are recorded as `granted_network_ids` so that expiry removes
   * exactly these and never access that came from somewhere else.
   */
  protected function grantNetworks()
  {
    $metadata = $this->product !== null ? json_decode((string) $this->product->metadata) : null;
    if (!is_object($metadata) || empty($metadata->network_ids)) {
      return;
    }
    $granted = [];
    foreach (array_unique(array_filter(array_map('intval', explode(',', (string) $metadata->network_ids)))) as $networkId) {
      if (\app\modules\network\models\NetworkPlayer::findOne(['network_id' => $networkId, 'player_id' => $this->player_id]) !== null) {
        continue;
      }
      $np = new \app\modules\network\models\NetworkPlayer();
      $np->player_id = $this->player_id;
      $np->network_id = $networkId;
      $np->created_at = new Expression('NOW()');
      $np->updated_at = new Expression('NOW()');
      if ($np->save()) {
        $granted[] = $networkId;
      }
    }
    $own = json_decode((string) $this->metadata);
    if (!is_object($own)) {
      $own = new \stdClass();
    }
    $own->granted_network_ids = $granted;
    $this->updateAttributes(['metadata' => json_encode($own)]);
  }
}
