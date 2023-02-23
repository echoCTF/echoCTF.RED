<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "player_counter_nf".
 *
 * @property int $player_id
 * @property string $metric
 * @property int $counter
 */
class PlayerCounterNf extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_counter_nf';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['metric'], 'required'],
            [['counter'], 'integer'],
            [['metric'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => Yii::t('app', 'Player ID'),
            'metric' => Yii::t('app', 'Metric'),
            'counter' => Yii::t('app', 'Counter'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    public function distinctMetrics()
    {
        $ret=[];
        $metrics=\yii\helpers\ArrayHelper::getColumn($this->db->createCommand("SELECT DISTINCT metric FROM player_counter_nf")->queryAll(),'metric');
        foreach($metrics as $val)
        {
            $ret[$val]=$val;
        }
        return $ret;
    }
    /**
     * {@inheritdoc}
     * @return PlayerCounterNfQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerCounterNfQuery(get_called_class());
    }
}
