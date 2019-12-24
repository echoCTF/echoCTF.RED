<?php

namespace app\modules\gameplay\models;

use Yii;
use app\modules\frontend\models\Player;
/**
 * This is the model class for table "network_player".
 *
 * @property int $network_id
 * @property int $player_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Network $network
 * @property Player $player
 */
class NetworkPlayer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_player';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['network_id', 'player_id'], 'required'],
            [['network_id', 'player_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['network_id', 'player_id'], 'unique', 'targetAttribute' => ['network_id', 'player_id']],
            [['network_id'], 'exist', 'skipOnError' => true, 'targetClass' => Network::className(), 'targetAttribute' => ['network_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
        ];
    }
    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'network_id' => 'Network ID',
            'player_id' => 'Player ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::className(), ['id' => 'network_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }
}
