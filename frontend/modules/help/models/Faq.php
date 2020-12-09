<?php

namespace app\modules\help\models;

use Yii;

/**
 * This is the model class for table "faq".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $body
 * @property int|null $weight
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
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'body' => Yii::t('app', 'Body'),
            'weight' => Yii::t('app', 'Weight'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return FaqQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FaqQuery(get_called_class());
    }

    public function save($runValidation=true, $attributeNames=null)
    {
        throw new \LogicException("Saving is disabled for this model.");
    }

}
