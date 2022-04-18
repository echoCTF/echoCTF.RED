<?php

namespace app\modules\settings\models;

use Yii;
use app\modules\frontend\models\Player;
/**
 * This is the model class for table "player_disabledroute".
 *
 * @property int $id
 * @property int|null $player_id
 * @property string|null $route
 *
 * @property Player $player
 */
class PlayerDisabledroute extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_disabledroute';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'integer'],
            [['route'], 'string', 'max' => 255],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'player_id' => Yii::t('app', 'Player ID'),
            'route' => Yii::t('app', 'Route'),
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|PlayerQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerDisabledrouteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerDisabledrouteQuery(get_called_class());
    }
}
