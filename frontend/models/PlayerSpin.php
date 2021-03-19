<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "player_spin".
 *
 * @property int $player_id
 * @property int|null $counter
 * @property int|null $total
 * @property string|null $updated_at
 * @property string $ts
 *
 * @property Player $player
 */
class PlayerSpin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_spin';
    }

    public function behaviors()
    {
        return [
          'typecast' => [
              'class' => AttributeTypecastBehavior::class,
              'attributeTypes' => [
                  'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                  'counter' => AttributeTypecastBehavior::TYPE_INTEGER,
                  'total' => AttributeTypecastBehavior::TYPE_INTEGER,
              ],
              'typecastAfterValidate' => true,
              'typecastBeforeSave' => false,
              'typecastAfterFind' => false,
          ],
          [
              'class' => TimestampBehavior::class,
              'createdAtAttribute' => 'updated_at',
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
            [['player_id'], 'required'],
            [['player_id', 'counter', 'total','perday'], 'integer'],
            [['updated_at', 'ts'], 'safe'],
            [['player_id'], 'unique'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'counter' => 'Counter',
            'total' => 'Total',
            'updated_at' => 'Updated At',
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
     * {@inheritdoc}
     * @return PlayerSpinQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerSpinQuery(get_called_class());
    }
}
