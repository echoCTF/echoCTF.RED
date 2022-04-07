<?php

namespace app\modules\sales\models;

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
            [['id', 'name','price_id'], 'required'],
            [['description', 'metadata','htmlOptions','perks','interval','price_id'], 'string'],
            [['weight','interval_count'],'integer'],
            [['active', 'livemode'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['id','shortcode'], 'string', 'max' => 40],
            [['name','price_id'], 'string', 'max' => 255],
            [['currency'],'default','value'=>'eur'],
            [['weight'],'default','value'=>0],
            [['interval_count'],'default','value'=>'12'],
        
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
     * Gets query for [[Network]].
     *
     * @return \yii\db\ActiveQuery|NetworkQuery
     */
    public function getProductNetworks()
    {
        return $this->hasMany(ProductNetwork::class, ['id' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuery(get_called_class());
    }
}
