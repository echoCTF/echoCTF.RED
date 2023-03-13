<?php

namespace app\modules\gameplay\models;

use Yii;
use app\modules\activity\models\SpinQueue;
use app\modules\activity\models\Headshot;
use app\modules\infrastructure\models\TargetInstance;
use app\modules\infrastructure\models\TargetMetadata;


/**
 * This is the model class for table "target".
 *
 * @property int $id target ID
 * @property string $name A name for the target
 * @property string $fqdn The FQDN for the target
 * @property string $purpose The purpose of this target
 * @property string $description
 * @property int $ip The IP of the target
 * @property string $mac The mac associated with this IP
 * @property int $active
 * @property string $status Status for the target (online,offline,powerup,powerdown)
 * @property string $scheduled_at A date to associate with "status" if needed
 * @property string $net Network this pod is attached
 * @property string $server Docker Server connection string.
 * @property string $image
 * @property string $dns
 * @property string $parameters
 * @property string $category
 * @property string $imageparams
 * @property int $suggested_xp
 * @property int $required_xp
 * @property int $rootable
 * @property int $difficulty
 * @property int $weight
 * @property bool $healthcheck
 * @property bool $writeup_allowed
 * @property bool $instance_allowed
 * @property bool $require_findings
 * @property string $created_at The date this record was created_at
 *
 * @property Finding[] $findings
 * @property TargetVariable[] $targetVariables
 * @property TargetVolume[] $targetVolumes
 * @property Treasure[] $treasures
 * @property Headshot[] $headshots
 * @property int $memory
 */
class TargetAR extends \yii\db\ActiveRecord
{
  public $ipoctet;
  public $statuses=[
    'online'=>'online',
    'offline'=>'offline',
    'powerup'=>'powerup',
    'powerdown'=>'powerdown',
    'maintenance'=>'maintenance'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parameters', 'description','imageparams'], 'string'],
            [['name', 'fqdn'], 'required'],
            [['ip', 'timer','active', 'rootable', 'difficulty', 'suggested_xp', 'required_xp','weight','healthcheck','writeup_allowed','player_spin','headshot_spin','instance_allowed','require_findings'], 'integer'],
            [['ipoctet'], 'ip'],
            [['name', 'fqdn', 'purpose', 'net', 'server', 'image', 'dns','category'], 'string', 'max' => 255],
            [['image'], 'filter', 'filter'=>'strtolower'],
            [['mac'], 'string', 'max' => 30],
            [['name'], 'unique'],
            [['fqdn'], 'unique'],
            [['mac'], 'unique'],
            [['status'], 'in', 'range' => ['online', 'offline', 'powerup', 'powerdown', 'maintenance']],
            [['status'], 'default', 'value'=> 'offline'],
            [['scheduled_at'], 'datetime', 'format'=>'php:Y-m-d H:i:s'],
            [['created_at'], 'datetime', 'format'=>'php:Y-m-d H:i:s'],
            [['weight'],'filter', 'filter' => 'intval'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'fqdn' => 'Fqdn',
            'purpose' => 'Purpose',
            'description' => 'Description',
            'ip' => 'IP',
            'mac' => 'Mac',
            'active' => 'Active',
            'net' => 'Net',
            'server' => 'Server',
            'image' => 'Image',
            'dns' => 'Dns',
            'parameters' => 'Parameters',
            'rootable' => 'Rootable',
            'difficulty' => 'Difficulty',
            'suggested_xp'=>'Suggested XP',
            'required_xp'=>'Required XP',
            'timer'=>'Timer',
            'weight'=>'Weight',
            'category'=>'Category'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFindings()
    {
        return $this->hasMany(Finding::class, ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkTarget()
    {
        return $this->hasOne(NetworkTarget::class, ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::class, ['id' => 'network_id'])->via('networkTarget');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetadata()
    {
        return $this->hasOne(TargetMetadata::class, ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargetVariables()
    {
        return $this->hasMany(TargetVariable::class, ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargetVolumes()
    {
        return $this->hasMany(TargetVolume::class, ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTreasures()
    {
        return $this->hasMany(Treasure::class, ['target_id' => 'id'])->orderBy(['weight' => SORT_DESC,'id'=>SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHeadshots()
    {
        return $this->hasMany(Headshot::class, ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstances()
    {
        return $this->hasMany(TargetInstance::class, ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpinQueue()
    {
        return $this->hasOne(SpinQueue::class, ['target_id' => 'id']);
    }

    public function afterFind() {
      parent::afterFind();
      $this->ipoctet=long2ip($this->ip);
    }


    public function beforeSave($insert)
    {
      if(!parent::beforeSave($insert))
      {
        return false;
      }

      if($insert && $this->created_at===null)
      {
          $this->created_at=new \yii\db\Expression('now()');
      }

      $this->ip=ip2long($this->ipoctet);

      return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if(!$this->isNewRecord)
        {
            if(array_key_exists('name',$changedAttributes))
            {
                Yii::$app->db->createCommand('UPDATE finding SET name=REPLACE(name,:search,:replace),pubname=REPLACE(pubname,:search,:replace) WHERE target_id=:tgt',[':search'=>$changedAttributes['name'],':replace'=>$this->name,':tgt'=>$this->id])->execute();
                Yii::$app->db->createCommand('UPDATE treasure SET name=REPLACE(name,:search,:replace),pubname=REPLACE(pubname,:search,:replace) WHERE target_id=:tgt',[':search'=>$changedAttributes['name'],':replace'=>$this->name,':tgt'=>$this->id])->execute();
            }
            if(array_key_exists('ip',$changedAttributes))
            {
                Yii::$app->db->createCommand('UPDATE finding SET name=REPLACE(name,:search,:replace),pubname=REPLACE(pubname,:search,:replace) WHERE target_id=:tgt',[':search'=>long2ip($changedAttributes['ip']),':replace'=>$this->ipoctet,':tgt'=>$this->id])->execute();
                Yii::$app->db->createCommand('UPDATE treasure SET name=REPLACE(name,:search,:replace),pubname=REPLACE(pubname,:search,:replace) WHERE target_id=:tgt',[':search'=>long2ip($changedAttributes['ip']),':replace'=>$this->ipoctet,':tgt'=>$this->id])->execute();
            }
        }
        parent::afterSave($insert,$changedAttributes);
    }

    /**
     * {@inheritdoc}
     * @return TargetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TargetQuery(get_called_class());
    }

    /*
     * Get Target Ondemand relations of target
     */
    public function getOndemand()
    {
      return $this->hasOne(TargetOndemand::class, ['target_id' => 'id'])->withExpired();
    }

}
