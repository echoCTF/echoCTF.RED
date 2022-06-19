<?php

namespace app\modules\content\models;

use Yii;

/**
 * This is the model class for table "layout_override".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $route
 * @property int|null $guest
 * @property int|null $repeating
 * @property int|null $player_id
 * @property string|null $css
 * @property string|null $js
 * @property string|null $valid_from
 * @property string|null $valid_until
 */
class LayoutOverride extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'layout_override';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'integer'],
            [['guest','repeating'],'boolean'],
            [['css', 'js'], 'string'],
            [['guest','repeating'],'default','value'=>0],
            [['valid_from', 'valid_until'], 'safe'],
            [['name', 'route'], 'string', 'max' => 255],
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
            'route' => Yii::t('app', 'Route'),
            'guest' => Yii::t('app', 'Guest'),
            'player_id' => Yii::t('app', 'Player ID'),
            'css' => Yii::t('app', 'Css'),
            'js' => Yii::t('app', 'Js'),
            'valid_from' => Yii::t('app', 'Valid From'),
            'valid_until' => Yii::t('app', 'Valid Until'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return LayoutOverrideQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LayoutOverrideQuery(get_called_class());
    }
}
