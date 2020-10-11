<?php

namespace app\modules\team\models;

use Yii;
#use app\modules\frontend\models\Team;

/**
 * This is the model class for table "team_score".
 *
 * @property int $team_id
 * @property int $points
 */
class TeamScore extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team_score';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id'], 'required'],
            [['team_id', 'points'], 'integer'],
            [['team_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'team_id' => 'Team ID',
            'points' => 'Points',
        ];
    }

    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }

}
