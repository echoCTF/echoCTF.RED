<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "player_hint".
 *
 * @property int $player_id
 * @property int $hint_id
 * @property int|null $status
 * @property string $ts
 *
 * @property Player $player
 * @property Hint $hint
 */
class PlayerHint extends \yii\db\ActiveRecord
{
    public $title;
    public $message;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_hint';
    }
    public function behaviors()
    {
      return [
        'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'hint_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'status'=>AttributeTypecastBehavior::TYPE_BOOLEAN,
            ],
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => false,
            'typecastAfterFind' => true,
        ],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'hint_id'], 'required'],
            [['player_id', 'hint_id', 'status'], 'integer'],
            [['ts', 'title', 'message'], 'safe'],
            [['player_id', 'hint_id'], 'unique', 'targetAttribute' => ['player_id', 'hint_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['hint_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hint::class, 'targetAttribute' => ['hint_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'hint_id' => 'Hint ID',
            'status' => 'Status',
            'ts' => 'Ts',
            'title'=>'title,'
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
    public function getHint()
    {
        return $this->hasOne(Hint::class, ['id' => 'hint_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerHintQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerHintQuery(get_called_class());
    }
    public function fields() {
      return ['hint_id', 'player_id', 'ts', 'status', 'title', 'message'];
    }
}
