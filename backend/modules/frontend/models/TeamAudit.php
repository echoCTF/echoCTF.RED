<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "team_audit".
 *
 * @property int $id
 * @property int|null $team_id
 * @property string|null $action
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id'], 'integer'],
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
            'action' => Yii::t('app', 'Action'),
            'message' => Yii::t('app', 'Message'),
            'ts' => Yii::t('app', 'Ts'),
        ];
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
