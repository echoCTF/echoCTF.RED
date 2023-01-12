<?php

namespace app\modules\game\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use app\models\Player;

/**
 * This is the model class for table "player_score_monthly".
 *
 * @property int $player_id
 * @property int $dated_at
 * @property int $points
 * @property string $ts
 */
class PlayerScoreMonthly extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_score_monthly';
    }
    public function behaviors()
    {
        return [
          'typecast' => [
              'class' => AttributeTypecastBehavior::class,
              'attributeTypes' => [
                'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'dated_at' => AttributeTypecastBehavior::TYPE_INTEGER,
                'points' => AttributeTypecastBehavior::TYPE_INTEGER,
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
            [['player_id','dated_at'], 'required'],
            [['player_id', 'points','dated_at'], 'integer'],
            [['ts'], 'safe'],
            ['player_id','unique', 'targetAttribute' => ['player_id', 'dated_at']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'points' => 'Points',
            'dated_at' => 'dated_at',
            'ts' => 'Ts',
        ];
    }
    public function getPlayer()
    {
      return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    public function getAvatar()
    {
        return '/images/avatars/'.$this->player->profile->avatar;
    }

    public static function find() {    return new PlayerScoreMonthlyQuery(get_called_class());}

}
