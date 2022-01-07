<?php

namespace app\modules\activity\models;

use Yii;

/**
 * This is the model class for table "team_stream".
 *
 * @property int $team_id
 * @property string $model
 * @property int $model_id
 * @property float $points
 * @property string $ts
 */
class TeamStream extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team_stream';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'model', 'model_id'], 'required'],
            [['team_id', 'model_id'], 'integer'],
            [['points'], 'number'],
            [['ts'], 'safe'],
            [['model'], 'string', 'max' => 255],
            [['team_id', 'model', 'model_id'], 'unique', 'targetAttribute' => ['team_id', 'model', 'model_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'team_id' => Yii::t('app', 'Team ID'),
            'model' => Yii::t('app', 'Model'),
            'model_id' => Yii::t('app', 'Model ID'),
            'points' => Yii::t('app', 'Points'),
            'ts' => Yii::t('app', 'Ts'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TeamStreamQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TeamStreamQuery(get_called_class());
    }
}
