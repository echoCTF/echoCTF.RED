<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\PlayerIp;
use app\modules\gameplay\models\Target;
use app\modules\settings\models\Sysconfig;
use yii\console\Exception as ConsoleException;

class SysconfigController extends Controller {

/*
  public function actionProfile($profile=false)
  {
    $profiles=[
      'devel' => [
        'mac_auth'=>true,
        'teams'=>true,
        'require_activation'=>true,
        'disable_registration'=>false,
        'strict_activation'=>false,
        'award_points'=>'full',
        'player_profile'=>false,
        'trust_user_ip'=>false,
        'offense_domain'=>'echoctf.red',
      ],
      'vpn' => [
        'award_points' => 'full',
        'disable_registration' => 0,
        'mac_auth' => 0,
        'player_profile' => 0,
        'require_activation' => 0,
        'strict_activation' => 0,
        'teams' => 1,
        'trust_user_ip' => 1,
      ],
      'mac' => [
        'award_points' => 'single',
        'join_team_with_token' => 1,
        'mac_auth' => 1,
        'player_profile' => 1,
        'require_activation' => 0,
        'strict_activation' => 0,
        'teams' => 1,
        'trust_user_ip' => 0,
      ]
    ];
    if($profile!==false)
    {
      printf("Setting %s profile parameters\n",$profile);
      foreach($profiles[$profile] as $key => $val )
      {
        $conf=Sysconfig::model()->findByAttributes(array('id'=>$key));
        printf("Processing option %s=>%d\n",$key,$val);
        if(!$conf)
        {
          $conf=new Sysconfig();
          $conf->id=$key;
          $conf->val=$val;
        }
        else
          $conf->val=$val;

        $conf->save();
      }
    }
  }
*/

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
