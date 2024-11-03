<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "player_token_history".
 *
 * @property int $id
 * @property int $player_id
 * @property string $type
 * @property string $token
 * @property string $description
 * @property string|null $expires_at
 * @property string $created_at
 * @property string $ts
 *
 * @property Player $player
 */
class PlayerTokenHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_token_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'token'], 'required'],
            [['player_id'], 'integer'],
            [['description'], 'string'],
            [['expires_at', 'created_at', 'ts'], 'safe'],
            [['type'], 'string', 'max' => 32],
            [['token'], 'string', 'max' => 128],
            [['token'], 'unique'],
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
            'type' => Yii::t('app', 'Type'),
            'token' => Yii::t('app', 'Token'),
            'description' => Yii::t('app', 'Description'),
            'expires_at' => Yii::t('app', 'Expires At'),
            'created_at' => Yii::t('app', 'Created At'),
            'ts' => Yii::t('app', 'Ts'),
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerTokenHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerTokenHistoryQuery(get_called_class());
    }
}
