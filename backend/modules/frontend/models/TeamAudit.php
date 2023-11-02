<?php

namespace app\modules\frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "team_audit".
 *
 * @property int $id
 * @property int $team_id
 * @property int|null $player_id
 * @property string $action
 * @property string|null $message
 * @property string $ts
 */
class TeamAudit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team_audit';
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
            [['team_id'], 'required'],
            [['team_id', 'player_id'], 'integer'],
            [['message'], 'string'],
            [['ts'], 'safe'],
            [['action'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'team_id' => Yii::t('app', 'Team ID'),
            'player_id' => Yii::t('app', 'Player ID'),
            'action' => Yii::t('app', 'Action'),
            'message' => Yii::t('app', 'Message'),
            'ts' => Yii::t('app', 'Ts'),
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
    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }

    /**
     * {@inheritdoc}
     * @return TeamAuditQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TeamAuditQuery(get_called_class());
    }
}
