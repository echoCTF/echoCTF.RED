<?php

namespace app\modules\sales\models;

use Yii;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "player_payment_history".
 *
 * @property int $id
 * @property int $player_id
 * @property string $product_id
 * @property string $price_id
 * @property int|null $amount
 * @property string|null $metadata
 * @property string|null $created_at
 */
class PlayerPaymentHistory extends \yii\db\ActiveRecord
{


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_payment_history';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['metadata', 'created_at'], 'default', 'value' => null],
      [['amount'], 'default', 'value' => 0],
      [['player_id', 'product_id', 'price_id'], 'required'],
      [['player_id', 'amount'], 'integer'],
      [['metadata', 'created_at'], 'safe'],
      [['product_id'], 'string', 'max' => 40],
      [['price_id'], 'string', 'max' => 32],
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
      'product_id' => Yii::t('app', 'Product ID'),
      'price_id' => Yii::t('app', 'Price ID'),
      'amount' => Yii::t('app', 'Amount'),
      'metadata' => Yii::t('app', 'Metadata'),
      'created_at' => Yii::t('app', 'Created At'),
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
   * {@inheritdoc}
   * @return PlayerPaymentHistoryQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerPaymentHistoryQuery(get_called_class());
  }
}
