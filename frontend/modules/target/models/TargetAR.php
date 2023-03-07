<?php

namespace app\modules\target\models;

use Yii;
use app\models\PlayerTreasure;
use app\models\PlayerFinding;
use app\modules\game\models\Headshot;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "target".
 *
 * @property int $id target ID
 * @property string|null $name A name for the target
 * @property string|null $fqdn The FQDN for the target
 * @property string|null $purpose The purpose of this target
 * @property string|null $description
 * @property int $ip The IP of the target
 * @property string|null $mac The mac associated with this IP
 * @property int|null $active
 * @property string|null $status
 * @property string|null $scheduled_at
 * @property string|null $net Network this pod is attached
 * @property string|null $server Docker Server connection string.
 * @property string|null $image
 * @property string|null $dns
 * @property string|null $parameters
 * @property int|null $rootable Whether the target is rootable or not
 * @property int|null $difficulty
 * @property int|null $suggested_xp
 * @property int|null $required_xp
 * @property string $ts
 * @property bool $timer
 * @property bool $instance_allowed
 * @property bool $writeup_allowed
 * @property bool $headshot_spin
 * @property bool $healthcheck
 * @property int|null $weight
 * @property float|null $player_rating
 *
 * @property Credential[] $credentials
 * @property Finding[] $findings
 * @property InfrastructureTarget $infrastructureTarget
 * @property Infrastructure[] $infrastructures
 * @property NetworkTarget[] $networkTargets
 * @property Network[] $networks
 * @property SpinHistory[] $spinHistories
 * @property SpinQueue $spinQueue
 * @property TargetVariable[] $targetVariables
 * @property TargetVolume[] $targetVolumes
 * @property Treasure[] $treasures
 * @property Headshot[] $headshots
 * @property Ondemand[] $ondemand
 * @property Writeup[] $writeups
 */
class TargetAR extends \app\models\ActiveRecordReadOnly
{
  public $total_treasures;
  public $total_findings;
  public $total_headshots;
  public $total_writeups;
  public $approved_writeups;
  public $player_findings;
  public $player_treasures;
  public $player_treasure_points;
  public $player_finding_points;
  public $player_points;
  public $player_rating;
  public $average_rating;
  public $ipoctet;
  public $progress;
  public $on_ondemand;
  public $ondemand_state;
  public $timer_avg;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
      return 'target';
    }


    public function behaviors()
    {
      return [
        'typecast' => [
          'class' => AttributeTypecastBehavior::class,
          'attributeTypes' => [
            'id' => AttributeTypecastBehavior::TYPE_INTEGER,
            'total_findings' => AttributeTypecastBehavior::TYPE_INTEGER,
            'player_findings' => AttributeTypecastBehavior::TYPE_INTEGER,
            'player_points' => AttributeTypecastBehavior::TYPE_INTEGER,
            'player_treasures' => AttributeTypecastBehavior::TYPE_INTEGER,
            'total_headshots' => AttributeTypecastBehavior::TYPE_INTEGER,
            'total_writeups' => AttributeTypecastBehavior::TYPE_INTEGER,
            'approved_writeups' => AttributeTypecastBehavior::TYPE_INTEGER,
            'total_treasures' => AttributeTypecastBehavior::TYPE_INTEGER,
            'progress' => AttributeTypecastBehavior::TYPE_FLOAT,
            'player_rating' => AttributeTypecastBehavior::TYPE_FLOAT,
            'average_rating' => AttributeTypecastBehavior::TYPE_INTEGER,
            'ip' => AttributeTypecastBehavior::TYPE_INTEGER,
            'active' => AttributeTypecastBehavior::TYPE_BOOLEAN,
            'writeup_allowed'=> AttributeTypecastBehavior::TYPE_BOOLEAN,
            'timer'=> AttributeTypecastBehavior::TYPE_BOOLEAN,
            'player_spin'=> AttributeTypecastBehavior::TYPE_BOOLEAN,
            'headshot_spin'=> AttributeTypecastBehavior::TYPE_BOOLEAN,
            'instance_allowed'=> AttributeTypecastBehavior::TYPE_BOOLEAN,
            'require_findings'=> AttributeTypecastBehavior::TYPE_BOOLEAN,
            'difficulty' => AttributeTypecastBehavior::TYPE_INTEGER,
            'weight' => AttributeTypecastBehavior::TYPE_INTEGER,
            'on_ondemand' => AttributeTypecastBehavior::TYPE_BOOLEAN,
            'ondemand_state' => AttributeTypecastBehavior::TYPE_INTEGER,
            'timer_avg'=> AttributeTypecastBehavior::TYPE_INTEGER,
          ],
          'skipOnNull'=>false,
          'typecastAfterValidate' => true,
          'typecastBeforeSave' => true,
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
        [['description'], 'string'],
        [['ip'], 'required'],
        [['ip', 'active', 'rootable', 'difficulty', 'suggested_xp', 'required_xp','weight','timer','writeup_allowed','player_spin','headshot_spin','instance_allowed','require_findings'], 'integer'],
        [['scheduled_at', 'ts'], 'safe'],
        [['name', 'fqdn', 'purpose', 'net', 'server', 'image', 'dns', 'parameters'], 'string', 'max' => 255],
        [['mac'], 'string', 'max' => 30],
        [['status'], 'string', 'max' => 32],
        [['name'], 'unique'],
        [['fqdn'], 'unique'],
        [['mac'], 'unique'],
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
        'fqdn' => 'FQDN',
        'purpose' => 'Purpose',
        'description' => 'Description',
        'ip' => 'IP',
        'mac' => 'Mac',
        'active' => 'Active',
        'status' => 'Status',
        'scheduled_at' => 'Scheduled At',
        'net' => 'Net',
        'server' => 'Server',
        'image' => 'Image',
        'dns' => 'Dns',
        'parameters' => 'Parameters',
        'rootable' => 'Rootable',
        'difficulty' => 'Difficulty',
        'suggested_xp' => 'Suggested Xp',
        'required_xp' => 'Required Xp',
        'ts' => 'Ts',
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
      return $this->hasMany(\app\modules\network\models\NetworkTarget::class, ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworks()
    {
      return $this->hasMany(\app\modules\network\models\Network::class, ['id' => 'network_id'])->viaTable('network_target', ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetwork()
    {
      return $this->hasOne(\app\modules\network\models\Network::class, ['id' => 'network_id'])->viaTable('network_target', ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTreasures()
    {
      return $this->hasMany(Treasure::class, ['target_id' => 'id'])->orderBy(['weight'=>SORT_DESC,'id'=>SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpinQueue()
    {
      return $this->hasOne(SpinQueue::class, ['target_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetadata()
    {
        return $this->hasOne(TargetMetadata::class, ['target_id' => 'id']);
    }


    /*
     * Get Headshot relations of target
     */
    public function getHeadshots()
    {
      return $this->hasMany(Headshot::class, ['target_id' => 'id'])->orderBy(['created_at'=>SORT_ASC]);
    }

    /*
     * Get Headshot relations of target in reverse ordr
     */
    public function getLastHeadshots()
    {
      if(Yii::$app->user->isGuest)
        $academic=0;
      else
        $academic=Yii::$app->user->identity->academic;
      return $this->hasMany(Headshot::class, ['target_id' => 'id'])->academic($academic)->orderBy(['created_at'=>SORT_DESC])->limit(50);
    }
    /*
     * Get Writeup relations of target
     */
    public function getWriteups()
    {
      return $this->hasMany(Writeup::class, ['target_id' => 'id'])->approved()->orderBy(['created_at'=>SORT_ASC]);
    }

    /*
     * Get Target Ondemand relations of target
     */
    public function getOndemand()
    {
      return $this->hasOne(Ondemand::class, ['target_id' => 'id'])->withExpired();
    }

    /*
     * Check if given $player_id has headshot on the target
     */
    public function headshot($player_id)
    {
      return Headshot::find()->where(['target_id' => $this->id, 'player_id'=>$player_id])->one();
    }

    public static function find()
    {
      return new TargetQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMigrationSchedule()
    {
      return $this->hasMany(\app\modules\network\models\NetworkTargetSchedule::class, ['target_id' => 'id']);
    }

    public function getScheduled()
    {
      return $this->hasOne(\app\modules\network\models\NetworkTargetSchedule::class, ['target_id' => 'id'])->pending()->limit(1);
    }

}
