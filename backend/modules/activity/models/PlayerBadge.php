<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Badge;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "player_badge".
 *
 * @property int $player_id
 * @property int $badge_id
 * @property string $ts
 *
 * @property Player $player
 * @property Badge $badge
 */
class PlayerBadge extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_badge';
    }
    public function behaviors()
    {
      return [
          [
              'class' => TimestampBehavior::class,
              'createdAtAttribute' => 'ts',
              'updatedAtAttribute' => 'ts',
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
            [['player_id', 'badge_id'], 'required'],
            [['player_id', 'badge_id'], 'integer'],
            [['ts'], 'safe'],
            [['player_id', 'badge_id'], 'unique', 'targetAttribute' => ['player_id', 'badge_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['badge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Badge::class, 'targetAttribute' => ['badge_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'badge_id' => 'Badge ID',
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
    public function getBadge()
    {
        return $this->hasOne(Badge::class, ['id' => 'badge_id']);
    }
}
