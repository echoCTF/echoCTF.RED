<?php

namespace app\modules\infrastructure\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;

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
    public $ipoctet;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target_instance_audit';
    }
    public function behaviors()
    {
        return [
          'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'reboot' => AttributeTypecastBehavior::TYPE_INTEGER,
                'team_allowed' => AttributeTypecastBehavior::TYPE_BOOLEAN,
            ],
            'typecastAfterValidate' => false,
            'typecastBeforeSave' => false,
            'typecastAfterFind' => true,
          ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'target_id'], 'required'],
            [['player_id', 'target_id', 'server_id', 'ip', 'reboot'], 'integer'],
            [['team_allowed'], 'boolean',],
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
            'ip' => Yii::t('app', 'IP'),
            'reboot' => Yii::t('app', 'Reboot'),
            'ts' => Yii::t('app', 'Ts'),
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|PlayerQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * Gets query for [[Target]].
     *
     * @return \yii\db\ActiveQuery|TargetQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * Gets query for [[Target]].
     *
     * @return \yii\db\ActiveQuery|TargetQuery
     */
    public function getServer()
    {
        return $this->hasOne(Server::class, ['id' => 'server_id']);
    }


    /**
     * {@inheritdoc}
     * @return TargetInstanceAuditQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TargetInstanceAuditQuery(get_called_class());
    }

    public function afterFind()
    {
        parent::afterFind();
        if($this->ip)
          $this->ipoctet=long2ip($this->ip);
    }

    public function getRebootVal()
    {
      if($this->reboot===0 && $this->ip===null)
      {
        return "Start";
      }
      elseif($this->reboot===0)
      {
        return "Do nothing";
      }
      elseif($this->reboot===1)
      {
        return "Restart";
      }
      elseif($this->reboot===2)
      {
        return "Destroy";
      }

    }
}
