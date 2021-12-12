<?php

namespace app\modelscli;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "banned_mx_servers".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $notes
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class BannedMxServer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banned_mx_server';
    }

    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'typecastAfterValidate' => false,
                'typecastBeforeSave' => true,
                'typecastAfterFind' => true,
          ],
          [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
          ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notes'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['created_at', 'updated_at'], 'safe'],
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
            'notes' => Yii::t('app', 'Notes'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return BannedMxServerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BannedMxServerQuery(get_called_class());
    }
}
