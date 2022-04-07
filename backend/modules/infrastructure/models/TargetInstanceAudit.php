<?php

namespace app\modules\infrastructure\models;

use Yii;

/**
 * This is the model class for table "target_instance_audit".
 *
 * @property int $id
 * @property string $op
 * @property int $player_id
 * @property int $target_id
 * @property int|null $server_id
 * @property int|null $ip
 * @property int $reboot
 * @property string $ts
 */
class TargetInstanceAudit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target_instance_audit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'target_id'], 'required'],
            [['player_id', 'target_id', 'server_id', 'ip', 'reboot'], 'integer'],
            [['ts'], 'safe'],
            [['op'], 'string', 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'op' => Yii::t('app', 'Op'),
            'player_id' => Yii::t('app', 'Player ID'),
            'target_id' => Yii::t('app', 'Target ID'),
            'server_id' => Yii::t('app', 'Server ID'),
            'ip' => Yii::t('app', 'Ip'),
            'reboot' => Yii::t('app', 'Reboot'),
            'ts' => Yii::t('app', 'Ts'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TargetInstanceAuditQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TargetInstanceAuditQuery(get_called_class());
    }
}
