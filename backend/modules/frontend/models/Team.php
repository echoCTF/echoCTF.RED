<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $academic
 * @property resource $logo
 * @property int $owner_id
 * @property string $token
 *
 * @property Player $owner
 * @property TeamPlayer[] $teamPlayers
 * @property Player[] $players
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'owner_id'], 'required'],
            [['description', 'logo'], 'string'],
            [['academic', 'owner_id'], 'integer'],
            [['academic'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['token'], 'string', 'max' => 30],
            [['token'], 'default', 'value' => substr(Yii::$app->security->generateRandomString(), 0, 30)],
            [['name'], 'unique'],
            [['token'], 'unique'],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['owner_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'academic' => 'Academic',
            'logo' => 'Logo',
            'owner_id' => 'Owner ID',
            'token' => 'Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Player::class, ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamPlayers()
    {
        return $this->hasMany(TeamPlayer::class, ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('team_player', ['team_id' => 'id']);
    }
}
