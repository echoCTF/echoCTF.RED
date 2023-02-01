<?php

namespace app\modules\help\models;

use Yii;

/**
 * This is the model class for table "credits".
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property int $weight
 */
class Credits extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'credits';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['body'], 'string'],
            [['weight'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'body' => 'Body',
            'weight' => 'Weight',
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        return;
    }
}
