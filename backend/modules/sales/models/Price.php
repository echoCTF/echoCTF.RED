<?php

namespace app\modules\sales\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "price".
 *
 * @property string $id
 * @property boolean $active
 * @property string #currency
 * @property string|null $metadata
 * @property string|null $nickname
 * @property string $product_id
 * @property string $recurring_interval
 * @property integer $interval_count
 * @property string $ptype
 * @property int unit_amount
 */
class Price extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'unique'],
            [['id','product_id','unit_amount','interval_count'], 'required'],
            [['recurring_interval','metadata','currency','nickname','product_id','ptype'], 'string'],
            [['active'], 'boolean'],
            [['recurring_interval'],'default','value'=>'month'],
            [['interval_count'],'default','value'=>1],
            [['currency'],'default','value'=>'eur'],
            [['metadata'],'default','value'=>'[]'],
            [['ptype'],'default','value'=>'recurring'],
            [['active'],'default','value'=>false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
          'id' => \Yii::t('app','ID'),
          'active' => \Yii::t('app','Active'),
          'currency' => \Yii::t('app','Currency'),
          'metadata' => \Yii::t('app','Metadata'),
          'nickname' => \Yii::t('app','Nickname'),
          'product_id' => \Yii::t('app','Product ID'),
          'recurring_interval' => \Yii::t('app','Recurring Interval'),
          'interval_count' => \Yii::t('app','Interval Count'),
          'ptype' => \Yii::t('app','Type'),
          'unit_amount' => \Yii::t('app','Unit Amount'),
        ];
    }
    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery|NetworkQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     * @return PriceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PriceQuery(get_called_class());
    }

}
