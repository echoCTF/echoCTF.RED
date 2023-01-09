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
            [['min_points','max_points'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['min_points','max_points'], 'compare', 'compareValue' => 2147483647, 'operator' => '<='],
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
}
