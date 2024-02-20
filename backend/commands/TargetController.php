<?php
namespace app\commands;

use yii\console\Controller;
use Docker\DockerClientFactory;
use app\modules\gameplay\models\Target;
use Docker\Docker;
use app\modules\infrastructure\models\TargetInstance;
use app\modules\infrastructure\models\DockerContainer;
use app\components\Pf;

class TargetController extends Controller {

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
      if($t->ondemand===null || $t->ondemand->state===1 || $target!==false )
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
   * Restart targets that are running for more than uptime minutes
   * Default value: 24 hours
   * @param int $uptime The container uptime to restart
   * @param int $limit The number of containers to restart
   */
  public function actionRestart($uptime=1440, $limit=1)
  {
    foreach(Target::find()->select(['server'])->distinct()->all() as $master)
    {
      if($master->server == null) continue;
      $client=DockerClientFactory::create([
        'remote_socket' => $master->server,
        'ssl' => false,
      ]);
      $docker=Docker::create($client);
      $containers=$docker->containerList();
      $processed=0;
      foreach($containers as $container)
      {
        if($processed>=$limit)
          break;
        $created=$container->getCreated();
        if($created<=(time()-intval($uptime*60)))
        {
          $name=str_replace('/', '', $container->getNames()[0]);
          $d=Target::findOne(['name'=>$name]);
          if($d)
          {
            printf("Restarting %s/%s [%s]\n", $master->server, $d->name, \Yii::$app->formatter->asRelativeTime($created));
            $d->spin();
          }
          $processed++;
        }
      }
    } // end docker servers
  }

  public function actionDestroyInstances($id=false,$dopf=false)
  {
    $t=TargetInstance::find();
    if(boolval($id)!==false)
      $t->where(['=','target_id',$id]);
    foreach($t->all() as $val)
    {
      $dc=new DockerContainer($val->target);
      $dc->name=$val->name;
      $dc->server=$val->server->connstr;
      printf(" %s for %s (%s)\n",$val->target->name,$val->player->username,$dc->name);
      try {
        if($val->ip!==null)
        {
          // ignore errors of destroy
          try { $dc->destroy(); } catch (\Exception $e) { }
        }
        try
        {
          if($dopf!==false)
          {
            Pf::kill_table($dc->name,true);
            Pf::kill_table($dc->name.'_clients',true);
          }
        } catch (\Exception $e) {}
        $val->delete();
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

  public function actionDestroyOndemand($id=false,$dopf=false)
  {
    try
    {
      $demands=\app\modules\gameplay\models\TargetOndemand::find();
      if(boolval($id)!==false)
      {
        $demands->where(['target_id'=>$id]);
      }
      else
      {
        $demands->andWhere(['state'=>1]);
      }

      foreach($demands->all() as $ondemand)
      {
        printf("Destroying ondemand target %s\n", $ondemand->target->fqdn);
        try { $ondemand->target->destroy(); } catch (\Exception $e) { }
        $ondemand->state=-1;
        $ondemand->heartbeat=null;
        if($dopf!==false)
          Pf::del_table_ip('heartbeat',$ondemand->target->ipoctet);
        $ondemand->save();
      }
    }
    catch (\Exception $e)
    {
      echo "OnDemand:", $e->getMessage(),"\n";
    }
  }
}
