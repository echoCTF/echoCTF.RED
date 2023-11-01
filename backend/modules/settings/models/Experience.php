<?php

namespace app\modules\settings\models;

use Yii;

/**
 * This is the model class for table "experience".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $category
 * @property string $icon
 * @property int $min_points
 * @property int $max_points
 */
class Experience extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'experience';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['min_points', 'max_points'], 'integer'],
            [['name', 'icon'], 'string', 'max' => 255],
            [['min_points','max_points'], 'compare', 'compareValue' => 0, 'operator' => '>=','type' => 'number'],
            [['min_points','max_points'], 'compare', 'compareValue' => 2147483647, 'operator' => '<=','type' => 'number'],
            [['min_points'], 'compare', 'compareAttribute' => 'max_points', 'operator' => '<=','type' => 'number'],
            [['min_points','max_points'], 'checkOverlap'],
            [['category'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'category' => 'Category',
            'description' => 'Description',
            'icon' => 'Icon',
            'min_points' => 'Min Points',
            'max_points' => 'Max Points',
        ];
    }

    public function checkOverlap($attribute, $params)
    {
        $conflicts=intval(self::find()->where($this->{$attribute}.' BETWEEN min_points AND max_points')->andWhere(['!=','id',$this->id])->count());
        if($conflicts>0)
            $this->addError($attribute, Yii::t('app', '{attribute} value {value} overlaps with another record.',['attribute'=>$attribute,'value'=>$this->{$attribute}]));
    }
}
