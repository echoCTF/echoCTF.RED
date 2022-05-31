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
}
