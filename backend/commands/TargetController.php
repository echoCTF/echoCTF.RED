<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use Docker\DockerClientFactory;
use app\modules\activity\models\SpinQueue;
use app\modules\activity\models\Notification;
use app\modules\activity\models\SpinHistory;
use app\modules\gameplay\models\Target;
use app\modules\gameplay\models\Finding;
use app\modules\settings\models\Sysconfig;
use app\components\Pf;
use Docker\Docker;
use Http\Client\Socket\Exception\ConnectionException;

class TargetController extends Controller {

  /*
   * Check for targets that need to power up based on their scheduled_at details
   * or targets that have been changed during the past $interval per $unit
   * (default is during the last 5 minutes)
   * @param int $interval.
   * @param string $unit (MySQL INTERVAL eg MONTH, DAY, HOUR, MINUTE, SECOND).
   */
  public function actionCron($interval=5, $unit="MINUTE")
  {
    // Check targets with scheduled_at>=NOW()
    $targets=Target::find();
    $targets->where(new \yii\db\Expression("IFNULL(scheduled_at,NOW())<NOW() OR (ts >= NOW() - INTERVAL {$interval} {$unit} and active=1)"))
    ->orWhere(new \yii\db\Expression("id IN (SELECT target_id FROM finding WHERE ts >=NOW() - INTERVAL {$interval} {$unit})"));
    //echo $targets->createCommand()->getRawSql();

    foreach($targets->all() as $target)
    {
      printf("Target %s ", $target->fqdn);
      switch($target->status)
      {
        case 'offline':
        case 'powerdown':
          printf("scheduled for [%s] at [%s]", $target->status, $target->scheduled_at);
          $target->powerdown();
          printf(", destroyed: %s\n", $requirePF ? "success" : "fail");
          break;
        case 'powerup':
          $target->pull();
          printf("scheduled for [%s] at %s, spin: %s\n", $target->status, $target->scheduled_at, $target->spin() ? "success" : "fail");
          $target->status='online';
          $target->scheduled_at=null;
          $target->active=1;
          $target->save();
          break;
        default:
          printf("updated at %s\n", $target->ts);
          break;
      }
      $this->actionPf(true);
    }
    //foreach target check
  }

  /*
   * Destroy a docker container for the given $target_id
   * @param int $target_id.
   */
  public function actionDestroy($target_id)
  {
    $target=Target::findOne($target_id);
    printf("Destroying target %s from %s: %s\n", $target->name, $target->server, $target->destroy() ? "success" : "fail");
  }

  /*
   * Spin target or targets.
   * This will try to spin the target even if it is not active
   * @param string $target.
   */
  public function actionSpin($target=false)
  {
    $query=Target::find();

    if($target !== false)
    {
      $query->andFilterWhere([
          'id' => intval($target),
      ]);
    }
    foreach($query->all() as $t)
    {
      echo "Restarting: ", $t->fqdn, " / ", $t->ipoctet;
      try
      {
        $t->spin();
        echo " OK\n";
      }
      catch(\Exception $ce)
      {
          printf(" NOT OK (%s)\n", $ce->getMessage());
      }
    }
  }

  /*
    Pull target/targets images on each of the docker servers
  */
  public function actionPull($target=false)
  {
    $query=Target::find();

    if($target !== false)
    {
      $query->andFilterWhere([
          'id' => $target,
      ]);
    }

    foreach($query->all() as $t)
    {
      echo "Pulling: ", $t->fqdn, " / ", $t->ipoctet;
      try
      {
        $t->pull();
        echo " OK\n";
      }
      catch(\Exception $ce)
      {
          printf(" NOT OK (%s)\n", $ce->getMessage());
      }
    }
  }

  /**
   *  Process Pending Spin Queue entries
   */
  public function actionSpinQueue()
  {

    $transaction=\Yii::$app->db->beginTransaction();
    try
    {
      $query=SpinQueue::find();
      foreach($query->all() as $t)
      {
        echo "Restarting: ", $t->target->fqdn, " / ", $t->target->ipoctet;
        try
        {
          $t->target->spin();
          $t->delete();
          $notification=new Notification;
          $notification->title=sprintf("Restart request for [%s/%s] completed.", $t->target->fqdn, $t->target->ipoctet);
          $notification->body=sprintf("Restart request for [%s/%s] completed.\nYour have performed %d resets out of %d for the day.", $t->target->fqdn, $t->target->ipoctet, $t->player->playerSpin->counter, Sysconfig::findOne('spins_per_day')->val);
          $notification->archived=0;
          $notification->player_id=$t->player_id;
          $notification->save();
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
        $transaction->rollBack();
        throw $e;
    }
    catch(\Throwable $e)
    {
        $transaction->rollBack();
        throw $e;
    }

  }

  /**
   * Check container health status and merge with spin queue
   */
  public function actionHealthcheck($spin=false)
  {
    $unhealthy=$this->unhealthy_dockers();
    $query=SpinQueue::find();
    foreach($query->all() as $t)
    {
      $unhealthy[$t->target->name]=$t->target;
    }

    foreach($unhealthy as $target)
    {
      printf("Processing [%s] on docker [%s]", $target->name, $target->server);
      if($target->spinQueue)
      {
        printf(" by [%s] on %s", $target->spinQueue->player->username, $target->spinQueue->created_at);
      }
      echo "\n";

      if($spin !== false)
      {
        $target->spin();
        if(!$target->spinQueue)
        {
          $sh=new SpinHistory;
          $sh->target_id=$target->id;
          $sh->created_at=new \yii\db\Expression('NOW()');
          $sh->updated_at=new \yii\db\Expression('NOW()');
          $sh->player_id=1;
          $sh->save();
        }
        else
        {
          $notif=new Notification;
          $notif->player_id=$target->spinQueue->player_id;
          $notif->title=sprintf("Target [%s] restart request completed", $target->name);
          $notif->body=sprintf("<p>The restart you requested, of [<b><code>%s</code></b>] is complete.<br/>Have fun</p>", $target->name);
          $notif->archived=0;
          $notif->created_at=new \yii\db\Expression('NOW()');
          $notif->updated_at=new \yii\db\Expression('NOW()');
          $notif->save();
        }
        SpinQueue::deleteAll(['target_id'=>$target->id]);
      }
    }
  }

  /**
   * Restart targets who are up for more than 24 hours
   */
  public function actionRestart()
  {
    foreach(Target::find()->docker_servers()->all() as $master)
    {
      if($master->server == null) continue;
      $client=DockerClientFactory::create([
        'remote_socket' => $master->server,
        'ssl' => false,
      ]);
      $docker=Docker::create($client);
      $containers=$docker->containerList();
      foreach($containers as $container)
      {
        $name=str_replace('/', '', $container->getNames()[0]);
        $d=Target::findOne(['name'=>$name]);
        $cstatus=$container->getStatus();
        if(preg_match('/Up ([0-9]+) hours/', $cstatus, $matches) !== false)
        {
          $hours=intval(@$matches[1]);
          if($hours >= 24)
          {
            printf("Restarting %s/%s [%s]\n", $master->server, $d->name, $cstatus);
            return $d->spin();
          }
        }
      }
    } // end docker servers
  }

  /**
   * Populate pf related tables and rules for targets
   */
  public function actionPf($load=false,$base="/etc")
  {
    $this->active_targets_pf($base);
    $this->match_findings($load,$base);
  }

  /*
   * Geneate match rules for target findings and load them
   */
  private function match_findings($load,$base="/etc")
  {
    $networks=$rules=$frules=array();
    $findings=Finding::find()->joinWith(['target'])->where(['target.active'=>true])->all();
    foreach($findings as $finding)
    {
      if($finding->target->network)
      {
        $networks[$finding->target->network->codename]=$finding->target->network;
      }
      $frules[]=$finding->matchRule;
    }

    Pf::store($base.'/match-findings-pf.conf',ArrayHelper::merge($frules,$rules));

    if($load)
      Pf::load_anchor_file("offense/findings","$base/match-findings-pf.conf");
  }

  /*
   * Find and store active targets IP addresses on their PF table
   */
  private function active_targets_pf($base="/etc")
  {
    $ips=$networks=$rules=array();
    $targets=Target::find()->where(['active'=>true])->all();
    foreach($targets as $target)
    {
      if($target->networkTarget === NULL)
        $ips[]=$target->ipoctet;
      else {
        $networks[$target->network->codename][]=$target->ipoctet;
        $rules[]=Pf::allowToNetwork($target);
        $rules[]=Pf::allowToClient($target);
      }
    }
    Pf::store($base.'/targets.conf',$ips);
    Pf::load_table_file('targets',$base.'/targets.conf');
    foreach($networks as $key => $val) {
      Pf::store($base.'/'.$key.'.conf', $val);
      Pf::load_table_file($key,$base.'/'.$key.'.conf');
      $rules[]=sprintf("pass inet proto udp from <%s> to (targets:0) port 53",$key);
    }

    Pf::store("$base/targets_networks.conf",$rules);
    Pf::load_anchor_file("targets/networks","$base/targets_networks.conf");
  }

  /*
   * Return an array of unhealthy containers or null
   */
  private function unhealthy_dockers()
  {
    $unhealthy=[];
    foreach(Target::find()->docker_servers()->all() as $target)
    {
      $docker=$this->docker_connect($target->server);

      $containers=$this->containers_list($docker);
      foreach($containers as $container)
      {
          if(strstr($container->getStatus(), 'unhealthy'))
          {
            $name=str_replace('/', '', $container->getNames()[0]);
            if(($unhealthyTarget=Target::findOne(['name'=>$name])) !== NULL)
              $unhealthy[$name]=$unhealthyTarget;
          }
      }
    }
    return $unhealthy;
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
      return [];
    }
    return $containerList;
  }

  /**
   * Connect to a docker server API and return docker client object
   */
  private function docker_connect($remote_socket)
  {
      $client=DockerClientFactory::create([
        'remote_socket' => $remote_socket,
        'ssl' => false,
      ]);
    try
    {
      $docker=Docker::create($client);
    }
    catch(\Exception $e)
    {
      return false;
    }
    return $docker;
  }
}
