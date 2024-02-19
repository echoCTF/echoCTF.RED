<?php
namespace app\commands;

use yii\console\Controller;
use yii\helpers\ArrayHelper;
use Docker\DockerClientFactory;
use app\modules\activity\models\SpinQueue;
use app\modules\activity\models\Notification;
use app\modules\activity\models\SpinHistory;
use app\modules\gameplay\models\Target;
use app\components\Pf;
use Docker\Docker;
use app\modules\infrastructure\models\DockerContainer;
use app\modules\infrastructure\models\TargetInstance;
use app\modules\infrastructure\models\TargetInstanceAudit;
use app\modules\infrastructure\models\NetworkTargetSchedule as NTS;
use app\modules\gameplay\models\NetworkTarget;
/**
 * @method docker_connect()
 * @method containers_list()
 */
class CronController extends Controller
{
  const ACTION_START=0;
  const ACTION_RESTART=1;
  const ACTION_DESTROY=2;
  const ACTION_EXPIRED=3;

  /*
   * Perofrm all needed cron operations for the VPN
   */
  public function actionIndex()
  {
    if(file_exists("/tmp/cron-index.lock"))
    {
      echo date("Y-m-d H:i:s ")."CronIndex: /tmp/cron-index.lock exists, skipping execution\n";
      return;
    }
    touch("/tmp/cron-index.lock");
    try {
      $this->actionTargetNetMigration();
      $this->actionSpinQueue();
      $this->actionOndemand();
      $this->actionPf(true);
    }
    catch(\Exception $e)
    {
      echo "cron/index: ".$e->getMessage(),"\n";
    }
    @unlink("/tmp/cron-index.lock");
  }

  public function actionTargetNetMigration()
  {
    if(file_exists("/tmp/cron-targetnetmigration.lock"))
    {
      echo date("Y-m-d H:i:s ")."TargetNetMigrations: /tmp/cron-targetnetmigration.lock exists, skipping execution\n";
      return;
    }
    touch("/tmp/cron-targetnetmigration.lock");
    try {
      // Get schedules that need to be processed
      $q=NTS::find()->queue();
      foreach($q->all() as $entry)
      {
        printf("Processing target: %s\n",$entry->target->name);

        // target scheduled to be removed from a network
        if($entry->network_id===null && $entry->target->network!==null)
        {
          printf("Removing from network %s\n",$entry->target->network->name);
          // remove target from old pf table
          // the rules and other pf entries will be synced
          Pf::del_table_ip($entry->target->network->codename,$entry->target->ipoctet);
          // short lived transactions
          $transaction = \Yii::$app->db->beginTransaction();
          try
          {
            $entry->trigger($entry::EVENT_TARGET_MIGRATE);
            $entry->target->networkTarget->delete();
            if($entry->delete()===false)
              throw new \Exception('Failed to delete migration for '.$entry->target->name);
            $transaction->commit();
          }
          catch(\Exception $e)
          {
            echo "Error: ",$e->getMessage();
            $transaction->rollBack();
          }
        }
        elseif($entry->network_id!==null)
        {
          printf("Moving to network %s\n",$entry->network->name);
          $transaction = \Yii::$app->db->beginTransaction();
          try
          {
            if($entry->target->network===null)
            {
              $NT=new NetworkTarget;
            }
            else
            {
              Pf::del_table_ip($entry->target->network->codename,$entry->target->ipoctet);
              $NT=$entry->target->networkTarget;
            }
            $NT->target_id=$entry->target_id;
            $NT->network_id=$entry->network_id;
            $entry->trigger($entry::EVENT_TARGET_MIGRATE);
            if($NT->save()===false || $entry->delete()===false)
              throw new \Exception('Failed migrate target ['.$entry->target->name.'] to network ',$entry->network->name);
            $transaction->commit();
          }
          catch (\Exception $e)
          {
            $transaction->rollBack();
          }

        }
      }
    }
    catch (\Exception $e)
    {
      echo "cron/target-net-migration: ",$e->getMessage(),"\n";
    }
    @unlink("/tmp/cron-targetnetmigration.lock");
  }

  public function actionPowerOperations()
  {
    if(file_exists("/tmp/cron-poweroperations.lock"))
    {
      echo date("Y-m-d H:i:s ")."PowerOperations: /tmp/cron-poweroperations.lock exists, skipping execution\n";
      return;
    }
    touch("/tmp/cron-poweroperations.lock");
    $this->actionPowerups();
    $this->actionPowerdowns();
    $this->actionOfflines();
    @unlink("/tmp/cron-poweroperations.lock");

  }

  public function actionInstancePf($before=60)
  {
    $action=SELF::ACTION_START;
    $t=TargetInstanceAudit::find()->where("ts>(now() - INTERVAL $before SECOND)")->orderBy(['id'=>SORT_ASC]);
    $ACTIONS=[];
    foreach($t->all() as $val)
    {
      $ACTION_ID=$val->player_id.'_'.$val->target_id;
      $ACTIONS[$ACTION_ID]=['op'=>$val->op,'object'=>$val];
    }
    foreach($ACTIONS as $obj)
    {
      $val=$obj['object'];
      $ips=[];
      if($val->op==='i')
      {
        echo date("Y-m-d H:i:s ")."Starting ",$val->id, " ",$val->target->name,'_',$val->player_id,"\n";
        $action=SELF::ACTION_START;
      }
      else if($val->op==='d')
      {
        echo date("Y-m-d H:i:s ")."Destroying ",$val->id, " ",$val->target->name,'_',$val->player_id," and its clients.\n";
        $action=SELF::ACTION_DESTROY;
      }
      else if($val->op==='u')
      {
        echo date("Y-m-d H:i:s ")."Updating ",$val->id, " ",$val->target->name,'_',$val->player_id,"\n";
        $action=SELF::ACTION_RESTART;
      }

      switch($action)
      {
        case SELF::ACTION_START:
        case SELF::ACTION_RESTART:
          if(($val->team_allowed===true && $val->player->teamPlayer && $val->player->teamPlayer->approved===1) || \Yii::$app->sys->team_visible_instances===true)
          {
            foreach($val->player->teamPlayer->team->teamPlayers as $teamPlayer)
            {
              if($teamPlayer->player->last->vpn_local_address!==null && $teamPlayer->player->last->vpn_local_address!==0 && $teamPlayer->approved===1)
              {
                $ips[]=long2ip($teamPlayer->player->last->vpn_local_address);
              }
            }
          }
          else if($val->player->last->vpn_local_address!==null && $val->player->last->vpn_local_address!==0)
          {
            $ips[]=long2ip($val->player->last->vpn_local_address);
          }
          if($ips)
          {
            echo 'Adding ',$val->target->name.'_'.$val->player_id.'_clients',' IP: ',implode(' ',$ips);
            Pf::add_table_ip($val->target->name.'_'.$val->player_id.'_clients',implode(' ',$ips),true);
          }
          if($val->ip!==null)
            Pf::add_table_ip($val->target->name.'_'.$val->player_id,long2ip($val->ip),true);
          else echo 'target not booted yet skipping table creation: ',$val->target->name.'_'.$val->player_id,"\n";
          break;
        case SELF::ACTION_EXPIRED:
        case SELF::ACTION_DESTROY:
            Pf::kill_table($val->target->name.'_'.$val->player_id,true);
            Pf::kill_table($val->target->name.'_'.$val->player_id.'_clients',true);
          break;
        default:
          printf("Error: Unknown action\n");
      }
    }
  }

  /**
   * Process player private instances
   */
  public function actionInstances($pfonly=false)
  {
    if(file_exists("/tmp/cron-instances.lock"))
    {
      echo date("Y-m-d H:i:s ")."Instances: /tmp/cron-instances.lock exists, skipping execution\n";
      return;
    }
    touch("/tmp/cron-instances.lock");
    $action=SELF::ACTION_EXPIRED;
    try {
      // Get powered instances
      $t=TargetInstance::find()->active();
      foreach($t->all() as $instance)
      {
        if($instance->player->last->vpn_local_address!==null && $pfonly===false)
        {
          $instance->updateAttributes(['updated_at' => new \yii\db\Expression('NOW()')]);
        }
      }

      $t=TargetInstance::find()->pending_action(40);
      foreach($t->all() as $val)
      {
        $ips=[];
        $dc=new DockerContainer($val->target);
        $dc->timeout= ($val->server->timeout ? $val->server->timeout : $dc->timeout=2000);
        if($val->target->targetVolumes!==null)
          $dc->targetVolumes=$val->target->targetVolumes;
        if($val->target->targetVariables!==null)
          $dc->targetVariables=$val->target->targetVariables;
        $dc->name=$val->name;
        $dc->server=$val->server->connstr;
        if($val->ip==null)
        {
          echo date("Y-m-d H:i:s ")."Starting";
          $action=SELF::ACTION_START;
        }
        else if($val->reboot===1)
        {
          echo date("Y-m-d H:i:s ")."Restarting";
          $action=SELF::ACTION_RESTART;
        }
        else if($val->reboot===2)
        {
          echo date("Y-m-d H:i:s ")."Destroying";
          $action=SELF::ACTION_DESTROY;
        }
        else {
          echo date("Y-m-d H:i:s ")."Expiring";
        }
        printf(" %s for %s (%s)\n",$val->target->name,$val->player->username,$dc->name);
        try {
          switch($action)
          {
            case SELF::ACTION_START:
            case SELF::ACTION_RESTART:
              if($pfonly===false)
              {
                try {
                  $dc->destroy();
                } catch (\Exception $e) { }
                $dc->pull();
                $dc->spin();
              }
              if(($val->team_allowed===true || \Yii::$app->sys->team_visible_instances===true) && $val->player->teamPlayer )
              {
                if($val->player->teamPlayer->approved===1)
                {
                  foreach($val->player->teamPlayer->team->teamPlayers as $teamPlayer)
                  {
                    if($teamPlayer->player->last->vpn_local_address!==null && $teamPlayer->approved===1)
                    {
                      $ips[]=long2ip($teamPlayer->player->last->vpn_local_address);
                    }
                  }
                }
              }
              else if($val->player->last->vpn_local_address!==null)
              {
                $ips[]=long2ip($val->player->last->vpn_local_address);
              }
              if($ips!=[])
                Pf::add_table_ip($dc->name.'_clients',implode(' ',$ips),true);
              $val->ipoctet=$dc->container->getNetworkSettings()->getNetworks()->{$val->server->network}->getIPAddress();
              Pf::add_table_ip($dc->name,$val->ipoctet,true);
              $val->reboot=0;
              if($pfonly===false)
                $val->save();

              break;
            case SELF::ACTION_EXPIRED:
            case SELF::ACTION_DESTROY:
              if($pfonly===false)
              {
                try {
                  $dc->destroy();
                } catch (\Exception $e) {

                }
              }
              Pf::kill_table($dc->name,true);
              Pf::kill_table($dc->name.'_clients',true);
              if($pfonly===false)
                $val->delete();
              break;
            default:
              printf("Error: Unknown action\n");
          }

        }
        catch (\Exception $e)
        {
          if(method_exists($e,'getErrorResponse'))
            echo $e->getErrorResponse()->getMessage(),"\n";
          else
            echo $e->getMessage(),"\n";
        }
      }
    }
    catch (\Exception $e)
    {
      echo "Instances:",$e->getMessage();
    }

    @unlink("/tmp/cron-instances.lock");
  }

  /**
   * Check container health status and merge with spin queue
   */
  public function actionHealthcheck($spin=false)
  {
    if(file_exists("/tmp/cron-healthcheck.lock"))
    {
      echo date("Y-m-d H:i:s ")."Healthcheck: /tmp/cron-healthcheck.lock exists, skipping execution\n";
      return;
    }
    touch("/tmp/cron-healthcheck.lock");
    try
    {
      $unhealthy=$this->unhealthy_dockers();

      foreach($unhealthy as $target)
      {
        printf("Processing unhealthy [%s] on docker [%s]", $target->name, $target->server);
        if($target->healthcheck==0)
        {
          printf("... skipping by healthcheck flag\n");
        }
        echo "\n";
        if($spin !== false)
        {
          $target->spin();
          $sh=new SpinHistory;
          $sh->target_id=$target->id;
          $sh->created_at=new \yii\db\Expression('NOW()');
          $sh->updated_at=new \yii\db\Expression('NOW()');
          $sh->player_id=1;
          $sh->save();
        }
      }
    }
    catch(\Exception $e)
    {
      echo "Healthcheck:", $e->getMessage(),"\n";
    }
    @unlink("/tmp/cron-healthcheck.lock");
  }

  /**
   *  Process Pending Spin Queue entries
   */
  public function actionSpinQueue()
  {

    try
    {
      $transaction=\Yii::$app->db->beginTransaction();
      $query=SpinQueue::find();
      foreach($query->all() as $t)
      {
        printf("Processing queue [%s] on docker [%s]", $t->target->name, $t->target->server);
        printf(" by [%s] on %s", $t->player->username, $t->created_at);

        try
        {
          $t->target->spin();
          if($t->target->ondemand && $t->target->ondemand->state<0)
          {
            $t->target->ondemand->state=1;
            $t->target->ondemand->heartbeat=new \yii\db\Expression('NOW()');
            $t->target->ondemand->player_id=$t->player_id;
            $t->target->ondemand->save();
            Pf::add_table_ip('heartbeat',$t->target->ipoctet);
            $notifTitle=sprintf("Target [%s] powered up", $t->target->name);
          }
          else
          {
            $notifTitle=sprintf("Target [%s] restart request completed", $t->target->name);
          }

          $notif=new Notification;
          $notif->player_id=$t->player_id;
          $notif->title=$notifTitle;
          $notif->body=sprintf("<p>The restart you requested, of [<b><code>%s</code></b>] is complete.<br/>Have fun</p>", $t->target->name);
          $notif->archived=0;
          $notif->created_at=new \yii\db\Expression('NOW()');
          $notif->updated_at=new \yii\db\Expression('NOW()');
          $notif->save();

          $t->delete();
          echo " OK\n";
        }
        catch(\Exception $ce)
        {
          printf(" NOT OK (%s)\n", $ce->getMessage());
        }
      }
      $transaction->commit();
    }
    catch(\Exception $e)
    {
        echo "Failed to process SpinQueue: [{$e->getMessage()}]\n";
        $transaction->rollBack();
        throw $e;
    }
    catch(\Throwable $e)
    {
        $transaction->rollBack();
        throw $e;
    }

  }


  public function actionPowerups()
  {
    try {
      $targets=Target::find()->powerup();
      foreach($targets->all() as $target)
      {
        printf("Target %s ", $target->fqdn);
        try {
          if(!$target->ondemand)
          {
            printf("scheduled for [%s] at %s, spin: %s\n", $target->status, $target->scheduled_at, $target->spin() ? "success" : "fail");
          }
          else
          {
            printf("scheduled for [%s] at %s, pull: %s\n", $target->status, $target->scheduled_at, $target->pull() ? "success" : "fail");
          }
          $target->status='online';
          $target->scheduled_at=null;
          $target->active=1;
          $target->save();
        }
        catch (\Exception $e)
        {
          echo "fail. ",$e->getMessage(),"\n";
        }
      }
    }
    catch (\Exception $e)
    {
      echo "Powerups:", $e->getMessage(),"\n";
    }
  }

  public function actionOndemand($host="/var/run/memcached/memcached.sock",$port=0)
  {
    try{
      $demands=\app\modules\gameplay\models\TargetOndemand::find()->andWhere(['state'=>1]);
      $memcache = new \Memcached();
      $memcache->addServer($host,$port);
      foreach($demands->all() as $ondemand)
      {
        $val=$memcache->get('target_heartbeat:'.$ondemand->target->ipoctet);
        if($val!==false)
          $ondemand->updateAttributes(['heartbeat'=>new \yii\db\Expression('NOW()')]);
      }

    }
    catch (\Exception $e)
    {

    }
    try
    {
      $demands=\app\modules\gameplay\models\TargetOndemand::find()
      ->andWhere(
        ['and',
          ['state'=>1],
          ['OR',
            ['IS','heartbeat',new \yii\db\Expression('NULL')],
            ['<=','heartbeat',new \yii\db\Expression('NOW() - INTERVAL 1 HOUR')],
          ]
        ]);

      foreach($demands->all() as $ondemand)
      {
        printf("Destroying ondemand target %s\n", $ondemand->target->fqdn);
        $ondemand->target->destroy();
        $ondemand->state=-1;
        $ondemand->heartbeat=null;
        Pf::del_table_ip('heartbeat',$ondemand->target->ipoctet);
        $ondemand->save();
      }
    }
    catch (\Exception $e)
    {
      echo "OnDemand:", $e->getMessage(),"\n";
    }
  }

  public function actionOfflines()
  {
    try {
      $targets=Target::find()->offline();
      foreach($targets->all() as $target)
      {
        printf("Target %s ", $target->fqdn);
        printf("scheduled for [%s] at [%s]", $target->status, $target->scheduled_at);
        $requirePF=$target->powerdown();
        printf(", destroyed: %s\n", $requirePF ? "success" : "fail");
      }
    }
    catch (\Exception $e)
    {
      echo "Offlines:",$e->getMessage(),"\n";
    }
  }

  public function actionPowerdowns()
  {
    try{
      $targets=Target::find()->powerdown();
      foreach($targets->all() as $target)
      {
        printf("Target %s ", $target->fqdn);
        printf("scheduled for [%s] at [%s]", $target->status, $target->scheduled_at);
        $requirePF=$target->powerdown();
        printf(", destroyed: %s\n", $requirePF ? "success" : "fail");
      }
    }
    catch (\Exception $e)
    {
      echo "Powerdowns:",$e->getMessage(),"\n";
    }
  }

  /**
   * Populate pf related tables and rules for targets
   */
  public function actionPf($load=false,$base="/etc")
  {
    try {
      $this->active_targets_pf($base);
      $this->match_findings($load,$base);
    }
    catch (\Exception $e)
    {
      echo "PF:",$e->getMessage(),"\n";
    }
  }

  /*
   * Generate match rules for target findings and load them
   */
  private function match_findings($load,$base="/etc")
  {
    $pflogmin=intval(\Yii::$app->sys->pflog_min);
    $pflogmax=intval(\Yii::$app->sys->pflog_max);
    if($pflogmin===0)
      $pflogmin=$pflogmax=1;
    $networks=$rules=$frules=array();
    $targets=Target::find()->active()->online()->poweredup()->all();
    foreach($targets as $target)
    {
      foreach($target->findings as $finding)
      {
        if($target->network)
        {
          $networks[$target->network->codename]=$target->network;
        }
        $frules[]=$finding->matchRule;
      }
    }
    $instances=TargetInstance::find()->active();
    foreach($instances->all() as $ti)
      foreach($ti->target->findings as $f)
        $rules[]=$f->getMatchRule('<'.$ti->name.'_clients>','<'.$ti->name.'>',$pflogmin,$pflogmax);

    Pf::store($base.'/match-findings-pf.conf',ArrayHelper::merge($frules,$rules));

    if($load)
      Pf::load_anchor_file("offense/findings","$base/match-findings-pf.conf");
  }

  /*
   * Find and store active targets IP addresses on their PF table
   */
  private function active_targets_pf($base="/etc")
  {
    $ips=$networks=$rules=$rulestoNet=$rulestoClient=[];
    $targets=Target::find()->active()->online()->poweredup()->orderBy(['ip'=>SORT_ASC])->all();
    foreach($targets as $target)
    {
      if($target->networkTarget === NULL)
      {
        $ips[]=$target->ipoctet;
      }
      else
      {
        $networks[$target->network->codename][]=$target->ipoctet;
        $rulestoNet[]=Pf::allowToNetwork($target);
        $rulestoClient[]=Pf::allowToClient($target);
      }
    }
    foreach(TargetInstance::find()->active()->all() as $ti)
    {
      $_nname=$ti->name;
      $networks[$_nname][]=$ti->ipoctet;
      $rulestoNet[]=Pf::allowToNetwork($ti->target,$_nname.'_clients',$_nname);
      $rulestoClient[]=Pf::allowToClient($ti->target,$_nname.'_clients',$_nname);
    }
    Pf::store($base.'/targets.conf',$ips);
    Pf::load_table_file('targets',$base.'/targets.conf');
    $rulestoNet=array_unique($rulestoNet,SORT_STRING);
    $rulestoClient=array_unique($rulestoClient,SORT_STRING);
    $rules=array_merge($rulestoNet,$rulestoClient);
    foreach($networks as $key => $val) {
      Pf::store($base.'/'.$key.'.conf', $val);
      Pf::load_table_file($key,$base.'/'.$key.'.conf');
      $rules[]=sprintf("pass inet proto udp from <%s> to (targets:0) port 53",$key);
    }

    if(Pf::store("$base/targets_networks.conf",$rules))
      Pf::load_anchor_file("networks","$base/targets_networks.conf");
  }

  /*
   * Return an array of unhealthy containers or null
   */
  private function unhealthy_dockers()
  {
    $unhealthy=[];
    foreach(Target::find()->select(['server'])->distinct()->all() as $target)
    {
      $docker=$this->docker_connect($target->server);

      $containers=$this->containers_list($docker);
      foreach($containers as $container)
      {
          if(strstr($container->getStatus(), 'unhealthy'))
          {
            $name=str_replace('/', '', $container->getNames()[0]);
            if(($unhealthyTarget=Target::findOne(['name'=>$name])) !== NULL)
            {
              $unhealthy[$name]=$unhealthyTarget;
            }
            else {
              echo date("Y-m-d H:i:s ")."Unhealthy container  [$name => {$target->server}] not on our list!!!\n";
            }
          }
      }
    }
    return $unhealthy;
  }

  /**
   * Connect to a docker server API and return docker client object
   */
  private function docker_connect($remote_socket)
  {
      $client=DockerClientFactory::create([
        'remote_socket' => $remote_socket,
        'ssl' => false,
        'timeout'=>5000,
      ]);
    try
    {
      $docker=Docker::create($client);
    }
    catch(\Exception $e)
    {
      echo "Failed to connect to docker server {$remote_socket}: [{$e->getMessage()}]\n";
      return false;
    }
    return $docker;
  }

  /**
   * Get a list of containers from a connected docker
   */
  private function containers_list($docker)
  {
    if($docker === false) return [];
    try
    {
      $containerList=$docker->containerList();
    }
    catch(\Exception $e)
    {
      echo "Failed to get container list: [{$e->getMessage()}]\n";
      return [];
    }
    return $containerList;
  }

}
