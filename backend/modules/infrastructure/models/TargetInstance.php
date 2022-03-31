<?php

namespace app\modules\infrastructure\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;

/**
 * This is the model class for table "target_instance".
 *
 * @property int $player_id
 * @property int $target_id
 * @property int|null $server_id
 * @property int|null $ip
 * @property int $reboot
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 * @property Target $target
 */
class TargetInstance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target_instance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'target_id'], 'required'],
            [['player_id', 'target_id', 'server_id', 'ip', 'reboot'], 'integer'],
            ['reboot','default','value'=>false],
            [['created_at', 'updated_at'], 'safe'],
            [['player_id'], 'unique'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::className(), 'targetAttribute' => ['target_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => Yii::t('app', 'Player ID'),
            'target_id' => Yii::t('app', 'Target ID'),
            'server_id' => Yii::t('app', 'Server ID'),
            'ip' => Yii::t('app', 'IP'),
            'reboot' => Yii::t('app', 'Reboot'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|PlayerQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }

    /**
     * Gets query for [[Target]].
     *
     * @return \yii\db\ActiveQuery|TargetQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::className(), ['id' => 'target_id']);
    }

    /**
     * {@inheritdoc}
     * @return TargetInstanceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TargetInstanceQuery(get_called_class());
    }
}
