<?php

namespace app\models;

use Yii;
use app\modules\target\models\Treasure;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "player_treasure".
 *
 * @property int $player_id
 * @property int $treasure_id
 * @property string $ts
 * @property float $points
*
 * @property Player $player
 * @property Treasure $treasure
 */
class PlayerTreasure extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_treasure';
    }

    public function behaviors()
    {
      return [
        'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'treasure_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'points' => AttributeTypecastBehavior::TYPE_FLOAT,
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
            [['player_id', 'treasure_id'], 'required'],
            [['player_id', 'treasure_id'], 'integer'],
            [['ts'], 'safe'],
            [['player_id', 'treasure_id'], 'unique', 'targetAttribute' => ['player_id', 'treasure_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['treasure_id'], 'exist', 'skipOnError' => true, 'targetClass' => Treasure::class, 'targetAttribute' => ['treasure_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'treasure_id' => 'Treasure ID',
            'ts' => 'Ts',
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
    public function getTreasure()
    {
        return $this->hasOne(Treasure::class, ['id' => 'treasure_id']);
    }
}
