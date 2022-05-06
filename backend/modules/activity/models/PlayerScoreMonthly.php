<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
/**
 * This is the model class for table "player_score_monthly".
 *
 * @property int $player_id
 * @property int $points
 * @property string $dated_at
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'dated_at'], 'required'],
            [['player_id', 'points'], 'integer'],
            [['dated_at', 'ts'], 'safe'],
            [['player_id', 'dated_at'], 'unique', 'targetAttribute' => ['player_id', 'dated_at']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => Yii::t('app', 'Player ID'),
            'points' => Yii::t('app', 'points'),
            'dated_at' => Yii::t('app', 'Dated At'),
            'ts' => Yii::t('app', 'Ts'),
        ];
    }

    public function getPlayer()
    {
      return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerScoreMonthlyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerScoreMonthlyQuery(get_called_class());
    }
}
