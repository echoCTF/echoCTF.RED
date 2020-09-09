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
 * This is the model class for table "writeup".
 *
 * @property int $player_id
 * @property int $target_id
 * @property resource|null $content
 * @property int|null $approved
 * @property string|null $status
 * @property resource|null $comment
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 * @property Target $target
 */
class Writeup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'writeup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'target_id'], 'required'],
            [['player_id', 'target_id'], 'integer'],
            [['approved'], 'boolean'],
            [['content', 'status', 'comment'], 'string'],
            ['status','in','range'=>['PENDING','NEEDS FIXES','REJECTED','OK']],
            [['created_at', 'updated_at'], 'safe'],
            [['player_id', 'target_id'], 'unique', 'targetAttribute' => ['player_id', 'target_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::className(), 'targetAttribute' => ['target_id' => 'id']],
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
                    'approved' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                ],
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
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'target_id' => 'Target ID',
            'content' => 'Content',
            'approved' => 'Approved',
            'status' => 'Status',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|Player
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }

    /**
     * Gets query for [[Target]].
     *
     * @return \yii\db\ActiveQuery|Target
     */
    public function getTarget()
    {
        return $this->hasOne(Target::className(), ['id' => 'target_id']);
    }

    /**
     * {@inheritdoc}
     * @return WriteupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WriteupQuery(get_called_class());
    }
}
