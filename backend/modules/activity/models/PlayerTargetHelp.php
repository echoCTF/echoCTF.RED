<?php

namespace app\modules\activity\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;
use yii\base\NotSupportedException;
use app\modules\gameplay\models\Target;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "player_target_help".
 *
 * @property int $player_id
 * @property int $target_id
 * @property string|null $created_at
 *
 * @property Player $player
 * @property Target $target
 */
class PlayerTargetHelp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_target_help';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['player_id', 'target_id'], 'required'],
            [['player_id', 'target_id'], 'integer'],
            [['created_at'], 'safe'],
            [['player_id', 'target_id'], 'unique', 'targetAttribute' => ['player_id', 'target_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
        ];
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
                'typecastAfterValidate' => false,
                'typecastBeforeSave' => true,
                'typecastAfterFind' => true,
          ],
          [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'value' => new Expression('NOW()'),
          ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'target_id' => 'Target ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|Player
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * Gets query for [[Target]].
     *
     * @return \yii\db\ActiveQuery|Target
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerTargetHelpQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerTargetHelpQuery(get_called_class());
    }
}
