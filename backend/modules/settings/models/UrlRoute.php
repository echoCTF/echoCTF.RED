<?php

namespace app\modules\settings\models;

use Yii;

/**
 * This is the model class for table "url_route".
 *
 * @property int $id
 * @property string $source
 * @property string $destination
 * @property int $weight
 */
class UrlRoute extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'url_route';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['source', 'destination'], 'required'],
            [['weight'], 'integer'],
            [['source', 'destination'], 'string', 'max' => 255],
            [['source'], 'unique'],
            ['weight','default','value'=>0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'source' => Yii::t('app', 'Source'),
            'destination' => Yii::t('app', 'Destination'),
            'weight' => Yii::t('app', 'Weight'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return UrlRouteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UrlRouteQuery(get_called_class());
    }
}
