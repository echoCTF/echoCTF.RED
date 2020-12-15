<?php

namespace app\models;

use Yii;
use app\modules\target\models\Finding;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "player_finding".
 *
 * @property int $player_id
 * @property int $finding_id
 * @property string $ts
 * @property float $points
 *
 * @property Player $player
 * @property Finding $finding
 */
class PlayerFinding extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_finding';
    }

    public function behaviors()
    {
      return [
        'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'finding_id' => AttributeTypecastBehavior::TYPE_INTEGER,
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
            [['player_id', 'finding_id'], 'required'],
            [['player_id', 'finding_id'], 'integer'],
            [['ts'], 'safe'],
            [['player_id', 'finding_id'], 'unique', 'targetAttribute' => ['player_id', 'finding_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['finding_id'], 'exist', 'skipOnError' => true, 'targetClass' => Finding::class, 'targetAttribute' => ['finding_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'finding_id' => 'Finding ID',
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
    public function getFinding()
    {
        return $this->hasOne(Finding::class, ['id' => 'finding_id']);
    }
}
