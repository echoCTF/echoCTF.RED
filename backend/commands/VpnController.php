<?php
/**
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @copyright 2022
 */

namespace app\commands;

use Yii;
use yii\console\Exception as ConsoleException;
use yii\helpers\Console;
use yii\console\Controller;
use app\modules\frontend\models\Player;
use app\modules\activity\models\PlayerLast;
use app\components\OpenVPN;
use yii\console\ExitCode;
/**
 * Manages VPN specific operations.
 *
 * @author proditis
 */
class VpnController extends Controller
{

  /**
   * IsOnline check
   * @param $string $player player: id or username to check
   */
  public function actionIsOnline($player)
  {
    $pM=Player::find()->where(['username'=>$player])->orWhere(['id'=>$player])->one();
    if($pM===NULL)
    {
      throw new ConsoleException(Yii::t('app', 'Player not found with id or username of [{values}]', ['values' => $player]));
    }

    $result=Yii::$app->db->createCommand("SELECT memc_get('ovpn:{$pM->id}') AS ovpn_status")->queryScalar();
    printf("Player %s %s\n",$pM->username,$result ?? "is offline");
  }

  /**
   * Logout a specific player from the database (no OpenVPN sessions are touched)
   * @param string $player player: id or username.
   */
  public function actionLogout($player)
  {

    $pM=Player::find()->where(['username'=>$player])->orWhere(['id'=>$player])->one();
    if($pM===NULL)
    {
      throw new ConsoleException(Yii::t('app', 'Player not found with id or username of [{values}]', ['values' => $player]));
    }
    printf("Logging out %d\n",$pM->id);
    OpenVPN::logout($pM->id);
  }

  /**
   * Kill a specific player session from OpenVPN.
   * @param string $player player: id or username.
   */
  public function actionKill($player)
  {

    $pM=Player::find()->where(['username'=>$player])->orWhere(['id'=>$player])->one();
    if($pM===NULL)
    {
      throw new ConsoleException(Yii::t('app', 'Player not found with id or username of [{values}]', ['values' => $player]));
    }
    printf("Killing %d with last local IP [%s]\n",$pM->id,$pM->last->vpn_local_address_octet);
    try {
      OpenVPN::kill($pM->id,intval($pM->last->vpn_local_address));
    }
    catch(\Exception $e)
    {
      echo "Error: ",$e->getMessage(),"\n";
      return ExitCode::UNSPECIFIED_ERROR;
    }

  }

  /**
   * Killall kill all connections from OpenVPN (takes a long time).
   */
  public function actionKillall()
  {

    foreach(PlayerLast::find()->all() as $pM)
    {
      try {
        if($pM->vpn_local_address!==null)
        {
          printf("Killing %s =>  %d\n",$pM->player->username,$pM->id);
          OpenVPN::kill($pM->id,intval($pM->vpn_local_address));
        }
      }
      catch(\Exception $e)
      {
        echo "Error: ",$e->getMessage(),"\n";
      }
    }
    Yii::$app->db->createCommand("UPDATE player_last SET vpn_local_address=NULL, vpn_remote_address=NULL")->execute();

  }
  /**
   * Logout all stall connections from all player_last entries
   */
  public function actionLogoutall()
  {
    Yii::$app->db->createCommand("UPDATE player_last SET vpn_local_address=NULL, vpn_remote_address=NULL")->execute();
  }

  /**
   * Load OpenVPN configuration from filesystem and store to the database.
   * Parses the configuration file for management interface and ranges.
   * @param string $filename The filename to read and store to the database
   */
  public function actionLoad($filepath)
  {
    $file=basename($filepath);
    $conf=file_get_contents($filepath);
    try{
      if(preg_match('/server (.*) (.*)/',$conf,$matches) && count($matches)>1)
      {
        $ovpnModel=\app\modules\settings\models\Openvpn::find()->where(['name'=>$file,'net'=>ip2long($matches[1])]);
      }
      if(($ovpn=$ovpnModel->one())===null)
      {
        $ovpn=new \app\modules\settings\models\Openvpn;
      }
      $ovpn->conf=file_get_contents($filepath);
      $ovpn->name=$file;
      $ovpn->server=gethostbyaddr(gethostbyname(gethostname()));
      if(preg_match('/status (.*)/',$conf,$matches) && count($matches)>1)
      {
        $ovpn->status_log=trim($matches[1]);
      }
      if(preg_match('/management (.*) (.*) (.*)/',$conf,$matches) && count($matches)>1)
      {
        $ovpn->management_ip_octet=$matches[1];
        $ovpn->management_port=$matches[2];
        if(str_starts_with($matches[3], '/'))
        {
          if(file_exists($matches[3]))
            $ovpn->management_passwd=file_get_contents($matches[3]);
          else
            echo "WARNING: The provided config uses a file as a management password\n\t but the file [",$matches[3], "] does not exist!\n";
        }
      }
      if(preg_match('/server (.*) (.*)/',$conf,$matches) && count($matches)>1)
      {
        $ovpn->net_octet=$matches[1];
        $ovpn->mask_octet=$matches[2];
      }
      if($ovpn->save())
      {
        echo $ovpn->isNewRecord ? "Record created successfully!\n" : "Record updated successfully!\n";
      }
      else
      {
        echo "Failed to save record: ",$ovpn->getErrorSummary(true),"\n";
      }
    }
    catch (\Exception $e)
    {
      printf("Error: %s",$e->getMessage());
    }
  }
  /**
   * Save OpenVPN configuration to filesystem.
   * Uses the provided file basename and current system hostname to find the actual entry.
   * @param string $filepath The full path to store the config contents.
   */
  public function actionSave($filepath)
  {
    try{
      $file=basename($filepath);
      $ovpnModel=\app\modules\settings\models\Openvpn::find()->where(['server'=>gethostname(),'name'=>$file]);
      if(($ovpn=$ovpnModel->one())===null)
      {
        echo "No record found for the given file and server!\n";
        return ExitCode::CANTCREAT;
      }
      if(file_put_contents($filepath,$ovpn->conf))
      {
        echo "File saved at ",$filepath,"\n";
      }
      else
      {
        echo "Failed to save ",$filepath,"\n";
        return ExitCode::UNSPECIFIED_ERROR;
      }
    }
    catch (\Exception $e)
    {
      printf("Error: %s",$e->getMessage());
    }
  }

  /**
   * Display openvpn status enriched with database details
   */
  public function actionStatus()
  {
    $q=\app\modules\settings\models\Openvpn::find()->select('status_log')->andFilterWhere(['server'=>gethostname()]);
    $status['routing_table']=[];
    $status['client_list']=[];
    foreach($q->all() as $entry)
    {
      try {
        $parsed=OpenVPN::parseStatus($entry->status_log);
        if(property_exists($parsed,'routing_table') && count($parsed->routing_table)>0)
          $status['routing_table']=\yii\helpers\ArrayHelper::merge($status['routing_table'],$parsed->routing_table);
        if(property_exists($parsed,'client_list') && count($parsed->client_list)>0)
          $status['client_list']=\yii\helpers\ArrayHelper::merge($status['client_list'],$parsed->client_list);
      }
      catch (\Exception $e)
      {
        printf("Error: %s",$e->getMessage());
      }
      unset($entry);
    }
    $this->stdout(sprintf("%-5s %-10s %-10s %-18s %-10s %-10s\n", 'ID', 'Username','Local IP','Remote IP', 'Received', 'Send'), Console::BOLD);
    foreach($status['client_list'] as $entry)
    {
      $p=\app\modules\frontend\models\Player::findOne($entry->player_id);
      $this->stdout(sprintf("%-5s %-10s %-10s %-18s %-10s %-10s\n", $entry->player_id,$p->username,$p->playerLast->vpn_local_address_octet,$entry->remote_ip_port,number_format($entry->bytes_received/1024).'kb',number_format($entry->bytes_send/1024).'kb'));
    }
  }
}
