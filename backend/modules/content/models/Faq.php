<?php

namespace app\modules\content\models;

use Yii;

/**
 * This is the model class for table "faq".
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property int $weight
 */
class Faq extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faq';
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
}
