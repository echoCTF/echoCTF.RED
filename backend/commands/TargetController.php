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
use yii\console\Exception as ConsoleException;

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



}
