<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\PlayerIp;
use app\modules\gameplay\models\Target;
use app\modules\settings\models\Sysconfig;

class EtcController extends Controller {

  /*
   * Store $contents to a $file and load the contents on pf $table
   */
  private function store_and_load($table,$file,$contents)
  {
    if(empty($contents)) return;
    file_put_contents($file,implode("\n",$contents));
    shell_exec("/sbin/pfctl -t $table -T replace -f $file");
  }

  /**
   * flush a given $table name
   */
  private function flush_pf_table($table)
  {
    printf("Flushing pf table [%s]\n",$table);
    shell_exec("/sbin/pfctl -t $table -T flush");
  }

  /*
   * Update /etc/targets.conf with an up to date list of active targets
   * and load the table
   */
  private function active_targets_pf()
  {
    $ips=array();
    $targets=Target::find()->where(['active'=>true])->all();
    foreach($targets as $target)
      $ips[]=$target->ipoctet;
    $this->store_and_load('targets','/etc/targets.conf',$ips);
  }

  /*
   * Generate and load PF Tables
   * If event_active sysconfig do not exist flush the tables
   */
  public function actionPftables()
  {

    $event_active=Sysconfig::findOne('event_active');
    if($event_active!==null && $event_active->val==='1')
    {
      $this->active_targets_pf();
    }
    else {
      printf("Event is not active, flushing tables\n");
      $this->flush_pf_table('offense_activated');
      $this->flush_pf_table('defense_activated');
      $this->flush_pf_table('targets');
    }
  }
}
