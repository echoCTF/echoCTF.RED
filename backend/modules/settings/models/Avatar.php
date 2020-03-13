<?php

namespace app\modules\settings\models;

use Yii;

/**
 * This is the model class for table "avatar".
 *
 * @property string $id
 */
class Avatar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'avatar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'string', 'max' => 32],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    /**
     * {@inheritdoc}
     * @return AvatarQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AvatarQuery(get_called_class());
    }
}
