<?php

namespace app\modules\target\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Player;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "spin_queue".
 *
 * @property int $target_id
 * @property int $player_id
 * @property string|null $created_at
 *
 * @property Player $player
 * @property Target $target
 */
class SpinQueue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'spin_queue';
    }

    public function behaviors()
    {
        return [
              'typecast' => [
                  'class' => AttributeTypecastBehavior::class,
                  'attributeTypes' => [
                      'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                      'target_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                  ],
                  'typecastAfterValidate' => true,
                  'typecastBeforeSave' => true,
                  'typecastAfterFind' => true,
            ],
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'created_at',
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
            [['target_id', 'player_id'], 'required'],
            [['target_id', 'player_id'], 'integer'],
            [['created_at'], 'safe'],
            [['target_id'], 'unique'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'target_id' => 'Target ID',
            'player_id' => 'Player ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * {@inheritdoc}
     * @return SpinQueueQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SpinQueueQuery(get_called_class());
    }
}
