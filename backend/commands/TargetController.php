<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use Docker\DockerClientFactory;
use app\modules\activity\models\SpinQueue;
use app\modules\activity\models\Notification;
use app\modules\activity\models\SpinHistory;
use app\modules\gameplay\models\Target;
use app\modules\gameplay\models\Finding;
use app\modules\settings\models\Sysconfig;
use Docker\Docker;
use Http\Client\Socket\Exception\ConnectionException;

class TargetController extends Controller {

  public function actionCron($interval=5,$unit="MINUTE")
  {
    // Check targets with scheduled_at>=NOW()
    $targets=Target::find();
    $targets->where(new \yii\db\Expression("IFNULL(scheduled_at,NOW())<NOW() OR (ts >= NOW() - INTERVAL {$interval} {$unit} and active=1)"))
    ->orWhere(new \yii\db\Expression("id IN (SELECT target_id FROM finding WHERE ts >=NOW() - INTERVAL {$interval} {$unit})"));
    //echo $targets->createCommand()->getRawSql();

    foreach($targets->all() as $target)
    {
      printf("Target %s ",$target->fqdn);
      $requirePF=false;
      switch($target->status)
      {
        case 'powerdown':
          printf("scheduled for [%s] at [%s] , destroyed: %s\n",$target->status,$target->scheduled_at,$target->destroy()?"success":"fail");
          $target->status='offline';
          $target->scheduled_at=null;
          $target->active=0;
          $requirePF=true;
          $target->save();
          break;
        case 'powerup':
          $target->pull();
          printf("scheduled for [%s] at %s, spin: %s\n",$target->status,$target->scheduled_at,$target->spin()?"success":"fail");
          $target->status='online';
          $target->scheduled_at=null;
          $target->active=1;
          $requirePF=true;
          $target->save();
          break;
        default:
          printf("updated at %s\n",$target->ts);
          $requirePF=true;
          break;
      }
      if($requirePF) $this->actionPf(true);
    }
    //foreach target check
  }
  public function actionDestroy($target_id)
  {
    $target = Target::findOne($target_id);
    printf("Destroying target %s from %s: %s\n",$target->name, $target->server, $target->destroy()? "success" : "fail");

  }
  /*
    Spin target/targets
  */
  public function actionSpin($target=false)
  {
    $query = Target::find();

    if($target!==false && mb_strtolower($target)!=='all')
    {
      $query->andFilterWhere([
          'id' => intval($target),
      ]);

    //  $query->andFilterWhere(['like', 'name', $target])
    //      ->andFilterWhere(['like', 'INET_NTOA(ip)', $target])
    //      ->andFilterWhere(['like', 'fqdn', $target])
    //      ->andFilterWhere(['like', 'server', $target]);
    }
    foreach($query->all() as $t)
    {
      echo "Restarting: ",$t->fqdn," / ",$t->ipoctet;
      try
      {
        $t->spin();
        echo " OK\n";
      }
      catch (ConnectionException | \LogicException $ce)
      {
          printf(" NOT OK (%s)\n",$ce->getMessage());
      }
    }

  }

  /*
    Pull target/targets images

  */
  public function actionPull($target=false,$filter=null)
  {
    $query = Target::find();

    if($target!==false && mb_strtolower($target)!=='all' && intval($target)!==0)
      $query->andFilterWhere([
          'id' => $target,
      ]);

    if($filter!=null)
      $query->andFilterWhere(['like', 'INET_NTOA(ip)', $target])
          ->orFilterWhere(['like', 'fqdn', $filter])
          ->orFilterWhere(['like', 'server', $filter]);

    foreach($query->all() as $t)
    {
      echo "Pulling: ",$t->fqdn," / ",$t->ipoctet;
      try
      {
        if($t->pull())
          echo " OK\n";
        else
          echo " Failed\n";
      }
      catch (ConnectionException | \LogicException $ce)
      {
          printf(" NOT OK (%s)\n",$ce->getMessage());
      }
    }
  }

  /**
   *  Process Pending Spin Queue entries
   */
  public function actionSpinQueue()
  {

    $transaction = \Yii::$app->db->beginTransaction();
    try {
      $query = SpinQueue::find();
      foreach($query->all() as $t)
      {
        echo "Restarting: ",$t->target->fqdn," / ",$t->target->ipoctet;
        try {
          $t->target->spin();
          $t->delete();
          $notification=new Notification;
          $notification->title=sprintf("Restart request for [%s/%s] completed.",$t->target->fqdn,$t->target->ipoctet);
          $notification->body=sprintf("Restart request for [%s/%s] completed.\nYour have performed %d resets out of %d for the day.",$t->target->fqdn,$t->target->ipoctet,$t->player->playerSpin->counter,Sysconfig::findOne('spins_per_day')->val);
          $notification->archived=0;
          $notification->player_id=$t->player_id;
          $notification->save();
          echo " OK\n";
        }
        catch (ConnectionException | \LogicException $ce)
        {
          printf(" NOT OK (%s)\n",$ce->getMessage());
        }
      }
      $transaction->commit();
    } catch (\Exception $e) {
        $transaction->rollBack();
        throw $e;
    } catch (\Throwable $e) {
        $transaction->rollBack();
        throw $e;
    }

  }
  /**
   * Check the status of running containers on each docker server.
   * $spin=true for restart on fail status
   */
  public function actionDockerHealthStatus($spin=false)
  {
    foreach(Target::find()->select('server')->distinct()->all() as $target)
    {
      echo "Connecting to: ",$target->server,"\n";
      $client = DockerClientFactory::create([
        'remote_socket' => $target->server,
        'ssl' => false,
      ]);
      $docker = Docker::create($client);
      $containers = $docker->containerList();
      //var_dump($containers);
      if(!$containers) echo "No containers found on {$target->server}\n";
      $unhealthy_found=false;
      foreach ($containers as $container) {
          if(strstr($container->getStatus(), 'unhealthy'))
          {
            $unhealthy_found=true;
            $name=str_replace('/','',$container->getNames()[0]);
            echo "$name unhealthy ";
            if(($target=Target::findOne(['name'=>$name]))!==NULL)
            {
              if($spin!==false)
              {
                echo "spining";
                $target->spin();
              }
            }
            echo "\n";
          }
      }
      if(!$unhealthy_found) echo "No unhealthy containers found\n";
    }
  }

  /**
   * Check container health status and merge with spin queue
   */
  public function actionHealthcheck($spin=false)
  {
    //$this->unhealthy=null;
    $unhealthy=$this->unhealthy_dockers();
    $query = SpinQueue::find();
    foreach($query->all() as $t)
    {
      $unhealthy[$t->target->name]=$t->target;
    }
    if($unhealthy)
    {
      foreach($unhealthy as $target)
      {
        printf("Processing [%s] on docker [%s]",$target->name,$target->server);
        if($target->spinQueue)
        {
          printf(" by [%s] on %s",$target->spinQueue->player->username,$target->spinQueue->created_at);
        }
        echo "\n";
        if($spin!==false)
        {
          $target->spin();
          if(!$target->spinQueue)
          {
            $sh=new SpinHistory;
            $sh->target_id=$target->id;
            $sh->created_at=new \yii\db\Expression('NOW()');
            $sh->updated_at=new \yii\db\Expression('NOW()');
            /* XXXFIXMEXXX Hardcoded uid this needs fixing */
            $sh->player_id=1;
            $sh->save();
          }
          else
          {
            $notif=new Notification;
            $notif->player_id=$target->spinQueue->player_id;
            $notif->title=sprintf("Target [%s] restart request completed",$target->name);
            $notif->body=sprintf("<p>The restart you requested, of [<b><code>%s</code></b>] is complete.<br/>Have fun</p>",$target->name);
            $notif->archived=0;
            $notif->created_at=new \yii\db\Expression('NOW()');
            $notif->updated_at=new \yii\db\Expression('NOW()');
            $notif->save();
          }
          SpinQueue::deleteAll(['target_id'=>$target->id]);
        }
      }
    }
  }
  /**
   * Populate pf related tables and rules for targets
   */
  public function actionPf($load=false)
  {
    $this->active_targets_pf();
    $this->match_findings($load);
  }


  private function match_findings($load)
  {
    $rules=array();
    $findings=Finding::find()->joinWith(['target'])->where(['target.active'=>true])->all();
    foreach($findings as $finding)
    {
      if($finding->protocol==='icmp')
        $rules[]=sprintf('match log (to pflog1) inet proto %s to %s tagged %s icmp-type echoreq label "$dstaddr:$dstport"',$finding->protocol,$finding->target->ipoctet,trim(Sysconfig::findOne('offense_registered_tag')->val));
      else
        $rules[]=sprintf('match log (to pflog1) inet proto %s to %s port %d tagged %s label "$dstaddr:$dstport"',$finding->protocol,$finding->target->ipoctet,$finding->port,trim(Sysconfig::findOne('offense_registered_tag')->val));
    }
    try {
      file_put_contents('/etc/match-findings-pf.conf',implode("\n",$rules)."\n");
    } catch (\Exception $e) {
      echo "Failed to save /etc/match-findings-pf.conf\n";
      return;
    }

    if($load)
      shell_exec("/sbin/pfctl -a offense/findings -Fr -f /etc/match-findings-pf.conf");
  }

  private function active_targets_pf()
  {
    $ips=array();
    $targets=Target::find()->where(['active'=>true])->all();
    foreach($targets as $target)
      $ips[]=$target->ipoctet;
    $this->store_and_load('targets','/etc/targets.conf',$ips);
  }

  /**
   * Store list of IP's to a file and load it on pf a pf table
   */
  private function store_and_load($table,$file,$contents)
  {
    if(empty($contents)) return;
    try {
      file_put_contents($file,implode("\n",$contents)."\n");
    }
    catch (\Exception $e)
    {
      echo "Failed to save {$file}\n";
      return;
    }
    shell_exec("/sbin/pfctl -t $table -T replace -f $file");
  }

  private function unhealthy_dockers()
  {
    $unhealthy=null;
    foreach(Target::find()->select('server')->distinct()->all() as $target)
    {
      if($target->server==null) continue;
      $client = DockerClientFactory::create([
        'remote_socket' => $target->server,
        'ssl' => false,
      ]);
      $docker = Docker::create($client);
      $containers = $docker->containerList();
      foreach ($containers as $container)
      {
          if(strstr($container->getStatus(), 'unhealthy'))
          {
            $name=str_replace('/','',$container->getNames()[0]);
            if(($target=Target::findOne(['name'=>$name]))!==NULL)
              $unhealthy[$name]=$target;
          }
      }
    }
    return $unhealthy;
  }
  /**
   * Restart targets who are up for more than 24 hours
   */
  public function actionRestart()
  {
    foreach(Target::find()->select('server')->distinct()->all() as $master)
    {
      if($master->server==null) continue;
      $client = DockerClientFactory::create([
        'remote_socket' => $master->server,
        'ssl' => false,
      ]);
      $docker = Docker::create($client);
      $containers = $docker->containerList();
      foreach ($containers as $container)
      {
        $name=str_replace('/','',$container->getNames()[0]);
        $d=Target::findOne(['name'=>$name]);
        $cstatus=$container->getStatus();
        if(preg_match('/Up ([0-9]+) hours/', $cstatus,$matches)!==false)
        {
          $hours=intval(@$matches[1]);
          if($hours>=24)
          {
            printf("Restarting %s/%s [%s]\n",$master->server,$d->name,$cstatus);
            return $d->spin();
          }
        }
      }
    } // end docker servers
  }

}
