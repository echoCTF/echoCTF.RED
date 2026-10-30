<?php

namespace app\modules\subscription\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property int $active
 * @property int $livemode
 * @property string|null $metadata
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Product extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'product';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'name'], 'required'],
      [['description', 'metadata', 'htmlOptions', 'perks', 'interval', 'currency'], 'string'],
      [['weight', 'interval_count'], 'integer'],
      [['weight'], 'default', 'value' => 0],
      [['active', 'livemode'], 'boolean'],
      [['created_at', 'updated_at'], 'safe'],
      [['id', 'shortcode'], 'string', 'max' => 40],
      [['name'], 'string', 'max' => 255],
      [['id'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'name' => Yii::t('app', 'Name'),
      'description' => Yii::t('app', 'Description'),
      'active' => Yii::t('app', 'Active'),
      'livemode' => Yii::t('app', 'Livemode'),
      'metadata' => Yii::t('app', 'Metadata'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
    ];
  }

  /**
   * {@inheritdoc}
   * @return ProductQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new ProductQuery(get_called_class());
  }
  /**
   * Gets query for [[Price]].
   *
   * @return \yii\db\ActiveQuery|PriceQuery
   */
  public function getPrices()
  {
    // Shortest billing period first (month, 6 months, year), one-off prices
    // first and then by amount (eg 1 day before 5 days)
    return $this->hasMany(Price::class, ['product_id' => 'id'])->andWhere(['price.active' => 1])
      ->orderBy([
        new \yii\db\Expression("CASE price.recurring_interval WHEN 'day' THEN 1 WHEN 'week' THEN 7 WHEN 'month' THEN 30 WHEN 'year' THEN 365 ELSE 0 END * price.interval_count"),
        'price.unit_amount' => SORT_ASC,
      ]);
  }

  public function inPrices($price_id)
  {
    return $this->hasMany(Price::class, ['product_id' => 'id'])->where(['price.id' => $price_id]);
  }

  public function htmlOptions($key)
  {
    $options = json_decode($this->htmlOptions);

    if (isset($options->{$key}) === true)
      return $options->{$key};
  }

  /**
   * Gets decoded json metadata for [[Product]].
   *
   * @return StdClass
   */
  public function getMetadataObj()
  {
    return json_decode($this->metadata);
  }
}
