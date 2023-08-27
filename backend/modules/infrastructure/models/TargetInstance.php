<?php

namespace app\modules\infrastructure\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;
use Docker\Docker;
use Docker\DockerClientFactory;
use Docker\API\Model\RestartPolicy;
use Docker\API\Model\HostConfig;
use Docker\API\Model\ContainersCreatePostBodyNetworkingConfig;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\EndpointIPAMConfig;

/**
 * This is the model class for table "target_instance".
 *
 * @property int $player_id
 * @property int $target_id
 * @property int|null $server_id
 * @property int|null $ip
 * @property int $reboot
 * @property bool $team_allowed
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
                'reboot' => AttributeTypecastBehavior::TYPE_INTEGER,
                'team_allowed' => AttributeTypecastBehavior::TYPE_BOOLEAN,
            ],
            'typecastAfterValidate' => false,
            'typecastBeforeSave' => true,
            'typecastAfterFind' => true,
          ],
          'timestamp'=>[
            'class'=>TimestampBehavior::class,
            'value' => new Expression('NOW()'),
          ]
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
            [['team_allowed'], 'boolean',],
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
            'team_allowed' => Yii::t('app', 'Team Allowed'),
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

    public function afterSave($insert, $changedAttributes)
    {
      parent::afterSave($insert, $changedAttributes);
      if($insert)
      {
        return;
      }
      if($this->ip!==null && array_key_exists('ip',$changedAttributes) && $changedAttributes['ip']===null)
      {
        $this->notify('started');
      }
      elseif($this->reboot===0 && @$changedAttributes['reboot']===1)
      {
        $this->notify('restarted');
      }
    }

    /**
    * Send notif after model deletion
    */
    public function afterDelete()
    {
        $this->notify('destroyed');
        parent::afterDelete();
    }

    public function notify($what)
    {
      $n=new \app\modules\activity\models\Notification;
      $n->player_id=$this->player_id;
      $n->title=Yii::t('app',"Your instance for target [{target_name}] got {what}.",['target_name'=>$this->target->name,'what'=>$what]);
      $n->body=$n->title;
      $n->archived=0;
      $n->created_at=new \yii\db\Expression('NOW()');
      $n->updated_at=new \yii\db\Expression('NOW()');
      $n->save();
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

    public function connectAPI($params=null)
    {
      if($params===null)
      {
        if($this->server!==null)
        {
          $params['remote_socket']=$this->server->connstr;
          $params['ssl']=false;
          $params['timeout']=5000;
        }
      }
      try
      {
        $client=DockerClientFactory::create($params);
        return Docker::create($client);
      }
      catch(\Exception $e)
      {
        return false;
      }

    }
  }
