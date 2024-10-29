<?php

namespace app\modules\frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "banned_player".
 *
 * @property int $id
 * @property int $old_id
 * @property string $username
 * @property string $email
 * @property string $registered_at
 * @property string $banned_at
 */
class BannedPlayer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banned_player';
    }

    public function behaviors()
    {
      return [
        'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'old_id' => AttributeTypecastBehavior::TYPE_INTEGER,
            ],
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => true,
            'typecastAfterFind' => true,
        ],
        [
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'banned_at',
            'updatedAtAttribute' => null,
            'value' => new \yii\db\Expression('NOW()'),
        ],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['old_id'], 'integer'],
            [['registered_at', 'banned_at'], 'safe'],
            [['username'], 'string', 'max' => 32],
            [['email'], 'string', 'max' => 128],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'old_id' => 'Old ID',
            'username' => 'Username',
            'email' => 'Email',
            'registered_at' => 'Registered At',
            'banned_at' => 'Banned At',
        ];
    }
}
