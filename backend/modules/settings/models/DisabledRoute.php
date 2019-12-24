<?php

namespace app\modules\settings\models;

use Yii;

/**
 * This is the model class for table "disabled_routes".
 *
 * @property string $route
 */
class DisabledRoute extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'disabled_route';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route'], 'required'],
            [['route'], 'string', 'max' => 255],
            [['route'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'route' => 'Route',
        ];
    }
}
