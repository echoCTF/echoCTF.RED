<?php

namespace app\modules\sales\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\base\UserException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "player_product".
 *
 * @property int $id
 * @property int $player_id
 * @property string $product_id
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
      [['player_id', 'price_id','product_id'], 'required'],
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
   * {@inheritdoc}
   * @return PlayerSubscriptionQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerProductQuery(get_called_class());
  }
}
