<?php

namespace app\modules\settings\models;

use Yii;

/**
 * This is the model class for table "sysconfig".
 *
 * @property string $id
 * @property resource $val
 */
class Sysconfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sysconfig';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['val'], 'string'],
            [['id'], 'string', 'max' => 255],
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
            'val' => 'Val',
        ];
    }
}
