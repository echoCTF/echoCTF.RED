<?php

namespace app\modules\sales\models;

use Yii;
use app\modules\gameplay\models\Network;

/**
 * This is the model class for table "product_network".
 *
 * @property string $product_id
 * @property int $network_id
 *
 * @property Network $network
 * @property Product $product
 */
class ProductNetwork extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_network';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'network_id'], 'required'],
            [['network_id'], 'integer'],
            [['product_id'], 'string', 'max' => 40],
            [['product_id', 'network_id'], 'unique', 'targetAttribute' => ['product_id', 'network_id']],
            [['network_id'], 'exist', 'skipOnError' => true, 'targetClass' => Network::class, 'targetAttribute' => ['network_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id' => Yii::t('app', 'Product ID'),
            'network_id' => Yii::t('app', 'Network ID'),
        ];
    }

    /**
     * Gets query for [[Network]].
     *
     * @return \yii\db\ActiveQuery|NetworkQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::class, ['id' => 'network_id']);
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
     * {@inheritdoc}
     * @return ProductNetworkQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductNetworkQuery(get_called_class());
    }
}
