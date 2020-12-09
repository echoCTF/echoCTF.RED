<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "experience".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $category
 * @property string|null $description
 * @property string|null $icon
 * @property int|null $min_points
 * @property int|null $max_points
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
    public function behaviors()
    {
      return [
        'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'min_points' => AttributeTypecastBehavior::TYPE_INTEGER,
                'max_points' => AttributeTypecastBehavior::TYPE_INTEGER,
            ],
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => false,
            'typecastAfterFind' => true,
        ],
      ];
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

    public function save($runValidation=true, $attributeNames=null)
    {
        throw new \LogicException("Saving is disabled for this model.");
    }

}
