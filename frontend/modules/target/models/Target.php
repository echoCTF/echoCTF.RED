<?php

namespace app\modules\target\models;

use Yii;
use app\models\PlayerTreasure;
use app\models\PlayerFinding;

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
 */
class Target extends \yii\db\ActiveRecord
{
    public  $total_treasures,
            $total_findings,
            $player_findings,
            $player_treasures,
            $player_treasure_points,
            $player_finding_points,
            $ipoctet,
            $progress;

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
            [['description'], 'string'],
            [['ip'], 'required'],
            [['ip', 'active', 'rootable', 'difficulty', 'suggested_xp', 'required_xp'], 'integer'],
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
    public function getCredentials()
    {
        return $this->hasMany(Credential::className(), ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFindings()
    {
        return $this->hasMany(Finding::className(), ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfrastructureTarget()
    {
        return $this->hasOne(InfrastructureTarget::className(), ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfrastructures()
    {
        return $this->hasMany(Infrastructure::className(), ['id' => 'infrastructure_id'])->viaTable('infrastructure_target', ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkTargets()
    {
        return $this->hasMany(NetworkTarget::className(), ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworks()
    {
        return $this->hasMany(Network::className(), ['id' => 'network_id'])->viaTable('network_target', ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTreasures()
    {
        return $this->hasMany(Treasure::className(), ['target_id' => 'id']);
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpinHistories()
    {
        return $this->hasMany(SpinHistory::className(), ['target_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpinQueue()
    {
      $command = Yii::$app->db->createCommand('select count(*) from spin_queue WHERE target_id=:target_id');
      return (int)$command->bindValue(':target_id',$this->id)->queryScalar();

        //return $this->hasOne(SpinQueue::className(), ['target_id' => 'id']);
    }

    public function getSchedule()
    {
      if(intval($this->active)===1 && $this->status==='powerdown')
        return sprintf('Target scheduled for powerdown at %s',$this->scheduled_at);
      elseif(intval($this->active)===0  && $this->status==='powerup' )
        return sprintf('Target scheduled for powerup at %s',$this->scheduled_at);
    }
    public function getDifficultyText()
    {
      switch($this->difficulty)
      {
        case 0: return "easy";
        case 1: return "easy/medium";
        case 2: return "medium";
        case 3: return "medium/advanced";
        case 4: return "advanced";
        case 5: return "hard";
        default: return "insanse";
      }
    }

    public function getPoints()
    {
      $sum_points=0;
      foreach($this->treasures as $tr)
      {
//        echo "t ",$tr->id," ",$tr->points,"\n";
        $sum_points+=$tr->points;
      }
      foreach($this->findings as $tr)
      {
//        echo "f ",$tr->id," ",$tr->points,"\n";
        $sum_points+=$tr->points;
      }
//        die(var_dump($sum_points));
      return $sum_points;
    }

    public function getHeadshots()
    {
      $command = Yii::$app->db->createCommand('select player_id as id from player_finding  as t1 WHERE finding_id IN (SELECT id FROM finding WHERE target_id=:target_id) GROUP BY player_id HAVING count(t1.finding_id)=(SELECT count(*) FROM finding WHERE target_id=:target_id)');
      $command->bindValue(':target_id',$this->id);
      $findings = $command->query();
      $command = Yii::$app->db->createCommand('select player_id as id from player_treasure  as t1 WHERE treasure_id IN (SELECT id FROM treasure WHERE target_id=:target_id) GROUP BY player_id HAVING count(t1.treasure_id)=(SELECT count(*) FROM treasure WHERE target_id=:target_id)');
      $command->bindValue(':target_id',$this->id);
      $treasures = $command->query();
      $tplayers=$fplayers=[];
      foreach($treasures->readAll() as $rec)
        $tplayers[]=$rec['id'];
      foreach($findings->readAll() as $rec)
        $fplayers[]=$rec['id'];
      return \app\models\Player::find()->where(['in','id',array_intersect($tplayers,$fplayers)])->all();
    }

    public function getCountHeadshots()
    {
      $command = Yii::$app->db->createCommand('select player_id as id from player_finding  as t1 WHERE finding_id IN (SELECT id FROM finding WHERE target_id=:target_id) GROUP BY player_id HAVING count(t1.finding_id)=(SELECT count(*) FROM finding WHERE target_id=:target_id)');
      $command->bindValue(':target_id',$this->id);
      $findings = $command->query();
      $command = Yii::$app->db->createCommand('select player_id as id from player_treasure  as t1 WHERE treasure_id IN (SELECT id FROM treasure WHERE target_id=:target_id) GROUP BY player_id HAVING count(t1.treasure_id)=(SELECT count(*) FROM treasure WHERE target_id=:target_id)');
      $command->bindValue(':target_id',$this->id);
      $treasures = $command->query();
      $tplayers=$fplayers=[];
      foreach($treasures->readAll() as $rec)
        $tplayers[]=$rec['id'];
      foreach($findings->readAll() as $rec)
        $fplayers[]=$rec['id'];
      return count(array_intersect($tplayers,$fplayers));
    }

    public function getFormattedExtras()
  	{
      $scheduled=null;
      if(intval($this->active)===1 && $this->status==='powerdown')
        $scheduled=sprintf('<abbr title="Scheduled to powedown at %s"><i class="glyphicon glyphicon-hand-down"></i></abbr>',$this->scheduled_at);
      elseif(intval($this->active)===0  && $this->status==='powerup' )
        $scheduled=sprintf('<abbr title="Scheduled to powerup %s"><i class="glyphicon glyphicon-hand-up"></i></abbr>',$this->scheduled_at);
  		return sprintf("<center><abbr title='Flags'><i class='material-icons'>flag</i>%d</abbr> / <abbr title='Service'><i class='material-icons'>whatshot</i>%d</abbr> / <abbr title='Headshots'><i class='material-icons'>memory</i>%d</abbr> %s</center>",count($this->treasures),count($this->findings),count($this->getHeadshots()),$scheduled);
  	}

    public function getSpinable()
    {
        if(Yii::$app->user->isGuest)
          return false;
        if($this->spinQueue!==0 || intval($this->active)!=1 )
          return false; // Not active or already queued
        if(intval(Yii::$app->user->identity->spins)>=intval(Yii::$app->sys->spins_per_day))
          return false; // user is not allowed spins for the day.
/* XXX FIXME XXX ADD MISSING DETAILS FOR SPINABLE */
        if(intval($this->player_findings)==0 && intval($this->player_treasures)==0 && Yii::$app->user->identity->profile->last->vpn_local_address===NULL)
          return false;
        return true;
    }
    public static function find()
    {
        return new TargetQuery(get_called_class());
    }

}
