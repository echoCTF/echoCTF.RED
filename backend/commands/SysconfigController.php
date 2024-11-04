<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use app\modules\settings\models\Sysconfig;
use yii\console\Exception as ConsoleException;

class SysconfigController extends Controller {

  /*
   * Set a sysconfig key to a specified value
   * @param string $key.
   * @param string $val.
   */
  public function actionSet($key, $val)
  {
      printf("Setting %s => %s\n", $key, $val);
      $conf=Sysconfig::findOne(['id'=>$key]);
      if($conf === null)
      {
        $conf=new Sysconfig();
        $conf->id=$key;
        $conf->val=$val;
      }
      else
        $conf->val=$val;

        $conf->save();
  }

  /**
   * Get a key.
   * @param string $key.
   */
  public function actionGet($key)
  {
    $conf=Sysconfig::findOne(['id'=>$key]);
    if($conf === null)
      return;

    echo $conf->val, "\n";
  }

}
