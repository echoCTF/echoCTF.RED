<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\PlayerIp;
use app\modules\gameplay\models\Target;
use app\modules\settings\models\Sysconfig;

class EtcController extends Controller {
  private function store_and_load($table,$file,$contents)
  {
    if(empty($contents)) return;
    file_put_contents($file,implode("\n",$contents));
    shell_exec("/sbin/pfctl -t $table -T replace -f $file");
  }
  private function active_users_pf()
  {
    $players=PlayerIp::find()->all();
    $offense=$defense=array();
    foreach($players as $player)
    {
        if($player->player->type == 'offense') $offense[]=long2ip($player->ip);
        else $defense[]=long2ip($player->ip);
    }
    $this->store_and_load('offense_activated','/etc/offense_activated.conf',$offense);
    $this->store_and_load('defense_activated','/etc/defense_activated.conf',$defense);
  }

  private function active_targets_pf()
  {
    $ips=array();
    $targets=Target::find()->where(['active'=>true])->all();
    foreach($targets as $target)
      $ips[]=$target->ipoctet;
    $this->store_and_load('targets','/etc/targets.conf',$ips);
  }
  /*
    Generate and load active PF Tables
  */
  public function actionPftables()
  {

    $event_active=Sysconfig::findOne('event_active');
    if($event_active!==null && $event_active->val==='1')
    {
      if(Sysconfig::findOne('trust_user_ip')!==null && Sysconfig::findOne('trust_user_ip')->val==='1') $this->active_users_pf();
      $this->active_targets_pf();
    }
    else {
      printf("event is not active\n");
      $this->flush_pf_table('offense_activated');
      $this->flush_pf_table('defense_activated');
      $this->flush_pf_table('targets');
    }
  }

  private function flush_pf_table($table)
  {
    printf("Flushing pf table [%s]\n",$table);
    shell_exec("/sbin/pfctl -t $table -T flush");
  }

  public function actionNpppdUsers()
  {
    // return or write /etc/npppd/pppd-users
  }

  public function actionBridgeRules()
  {
    $format="ifconfig %s flushrule %s rulefile /etc/%s-rules.conf";
    $val=Yii::app()->db->createCommand('CALL populate_bridge_rules()')->query();
    $br=BridgeRuleset::findAll();
    foreach($br as $rs)
      $rules[$rs->bridge_if][]=$rs->rule;

    file_put_contents("/etc/".Yii::app()->sys->offense_bridge_if."-rules.conf",implode("\n",$rules[Yii::app()->sys->offense_bridge_if]));
    $cmd=sprintf($format,Yii::app()->sys->offense_bridge_if, Yii::app()->sys->offense_eth_if,Yii::app()->sys->offense_bridge_if);
    shell_exec($cmd);

    if(array_key_exists(Yii::app()->sys->defense_bridge_if,$rules))
    {
      file_put_contents("/etc/".Yii::app()->sys->defense_bridge_if."-rules.conf",implode("\n",$rules[Yii::app()->sys->defense_bridge_if]));
      $cmd=sprintf($format,Yii::app()->sys->defense_bridge_if,Yii::app()->sys->defense_eth_if,Yii::app()->sys->defense_bridge_if);
      shell_exec($cmd);
    }

  }

}
