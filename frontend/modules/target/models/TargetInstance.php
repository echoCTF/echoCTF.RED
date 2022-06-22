<?php

namespace app\modules\target\models;

use Yii;
use app\models\Player;
use app\modules\target\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;

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
  public $ipoctet;
  const ACTION_START=0;
  const ACTION_RESTART=1;
  const ACTION_DESTROY=2;
  const ACTION_EXPIRED=3;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target_instance';
    }

    public function behaviors()
    {
        return [
          'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'ip' => AttributeTypecastBehavior::TYPE_INTEGER,
                ],
                'typecastAfterValidate' => true,
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
            [['ipoctet'], 'ip'],
            ['reboot','default','value'=>0],
            ['reboot','in','range'=>[0,1,2]],
            [['created_at', 'updated_at'], 'safe'],
            [['player_id'], 'unique'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
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
     * Gets name tha the target will have
     */
    public function getName()
    {
      return sprintf("%s_%d",strtolower($this->target->name),$this->player_id);
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
     * {@inheritdoc}
     * @return TargetInstanceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TargetInstanceQuery(get_called_class());
    }

    public function afterFind() {
      parent::afterFind();
      if($this->ip)
        $this->ipoctet=long2ip($this->ip);
    }

    public function beforeSave($insert)
    {
      if(parent::beforeSave($insert))
      {
          if($this->ipoctet)
            $this->ip=ip2long($this->ipoctet);
          else $this->ip=null;
          return true;
      }
      else
      {
          return false;
      }
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
