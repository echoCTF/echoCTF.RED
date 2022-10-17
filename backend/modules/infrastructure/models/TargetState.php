<?php

namespace app\modules\infrastructure\models;

use Yii;
use app\modules\gameplay\models\Target;
/**
 * This is the model class for table "target_state".
 *
 * @property int $id
 * @property int $total_headshots
 * @property int $total_findings
 * @property int $total_treasures
 * @property int $player_rating
 * @property int $timer_avg
 * @property int $total_writeups
 * @property int $approved_writeups
 * @property int $finding_points
 * @property int $treasure_points
 * @property int $total_points
 * @property int $on_network
 * @property int $on_ondemand
 * @property int $ondemand_state
 *
 * @property Target $target
 */
class TargetState extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target_state';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'total_headshots', 'total_findings', 'total_treasures', 'player_rating', 'timer_avg', 'total_writeups', 'approved_writeups', 'finding_points', 'treasure_points', 'total_points', 'on_network', 'on_ondemand', 'ondemand_state'], 'integer'],
            [['id'], 'unique'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'total_headshots' => Yii::t('app', 'Total Headshots'),
            'total_findings' => Yii::t('app', 'Total Findings'),
            'total_treasures' => Yii::t('app', 'Total Treasures'),
            'player_rating' => Yii::t('app', 'Player Rating'),
            'timer_avg' => Yii::t('app', 'Timer Avg'),
            'total_writeups' => Yii::t('app', 'Total Writeups'),
            'approved_writeups' => Yii::t('app', 'Approved Writeups'),
            'finding_points' => Yii::t('app', 'Finding Points'),
            'treasure_points' => Yii::t('app', 'Treasure Points'),
            'total_points' => Yii::t('app', 'Total Points'),
            'on_network' => Yii::t('app', 'On Network'),
            'on_ondemand' => Yii::t('app', 'On Ondemand'),
            'ondemand_state' => Yii::t('app', 'Ondemand State'),
        ];
    }

    /**
     * Gets query for [[Id0]].
     *
     * @return \yii\db\ActiveQuery|TargetQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return TargetStateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TargetStateQuery(get_called_class());
    }
}
