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

class CronController extends Controller {

  /*
   * Check for targets that need to power up based on their scheduled_at details
   * or targets that have been changed during the past $interval per $unit
   * (default is during the last 5 minutes)
   * @param int $interval.
   * @param string $unit (MySQL INTERVAL eg MONTH, DAY, HOUR, MINUTE, SECOND).
   */
  public function actionIndex($interval=5, $unit="MINUTE")
  {
    $this->actionPowerups();
    $this->actionPowerdowns();
    $this->actionOfflines();
    $this->actionPf();
    $this->run('target/healthcheck', ['1']);
  }


  public function actionPowerups()
  {
    $targets=Target::find()->powerup();
    foreach($targets->all() as $target)
    {
      printf("Target %s ", $target->fqdn);
      $target->pull();
      printf("scheduled for [%s] at %s, spin: %s\n", $target->status, $target->scheduled_at, $target->spin() ? "success" : "fail");
      $target->status='online';
      $target->scheduled_at=null;
      $target->active=1;
      $target->save();
    }
  }

  public function actionOfflines()
  {
    $targets=Target::find()->offline();
    foreach($targets->all() as $target)
    {
      printf("Target %s ", $target->fqdn);
      printf("scheduled for [%s] at [%s]", $target->status, $target->scheduled_at);
      $target->powerdown();
      printf(", destroyed: %s\n", $requirePF ? "success" : "fail");
    }
  }

  public function actionPowerdowns()
  {
    $targets=Target::find()->powerdown();
    foreach($targets->all() as $target)
    {
      printf("Target %s ", $target->fqdn);
      printf("scheduled for [%s] at [%s]", $target->status, $target->scheduled_at);
      $target->powerdown();
      printf(", destroyed: %s\n", $requirePF ? "success" : "fail");
    }
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

}
