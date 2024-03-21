<?php
namespace app\components;

use stdClass;
use Yii;

use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Handle Pf related tasks
 * @method load_table_file
 * @method load_anchor_file
 * @method store
 */
class OpenVPN extends Component
{
  public $debug;
  public function init()
  {
    $this->debug=getenv("DEBUG");
    parent::init();
  }
  /**
   * Logout player
   * @param integer $id player id
   */
  static public function logout(int $id)
  {
    Yii::$app->db->createCommand("UPDATE player_last SET vpn_local_address=NULL, vpn_remote_address=NULL WHERE id=:player",[':player'=>$id])->execute();
  }
  /**
   * Kill a player session from VPN
   * @param integer $player_id The ID of the player
   * @param integer|null $player_ip The current VPN IP of the player (if connected)
   */
  static public function kill(int $player_id,int $player_ip)
  {
    try
    {
      $creds = self::determineServerByAddr($player_ip);
      if($creds===null) return;
      $fp = fsockopen($creds->management_ip_octet, $creds->management_port, $errno, $errstr, 30);
      if (!$fp)
      {
        throw new \Exception("Error connecting to {$creds->management_ip_octet}:{$creds->management_port} $errstr ($errno)");
      }
      else
      {
        echo "connected to {$creds->management_ip_octet}\n";
        fwrite($fp, "$creds->management_passwd\n");
        echo "sending to ",$creds->management_ip_octet,"\n";
        usleep(250000);
        fwrite($fp, "kill $player_id\n");
        usleep(250000);
        fwrite($fp, "exit\n");
        usleep(250000);
        fclose($fp);
      }
    }
    catch (\Throwable $e)
    {
      echo "Error: ",$e->getMessage(),"\n";
    }
  }

  /**
   * Check the VPN server port based on the range assigned to the user
   * (if connected).
   * @param integer|null $player_ip IP of the player currently
   * @return Openvpn|null
   */
  static public function determineServerByAddr(int $player_ip)
  {
    return \app\modules\settings\models\Openvpn::find()->where(['net'=>new \yii\db\Expression(':player_ip & mask',[':player_ip'=>$player_ip])])->one();
  }

  static public function parseStatus(string $location)
  {
    if(!file_exists($location))
    {
      throw new yii\base\UserException("Status file does not exist");
    }
    $statusLines=explode("\n",file_get_contents($location));
    if(count($statusLines)==0)
      return new stdClass;
    $status=new stdClass;
    $status->updated_at=$statusLines[1];
    for($i=3;$i<count($statusLines);$i++)
    {
      if($statusLines[$i]==='ROUTING TABLE')
        break;
      $line=explode(',',$statusLines[$i]);
      $lineObj=new stdClass;
      $lineObj->player_id=intval($line[0]);
      $lineObj->remote_ip_port=$line[1];
      $lineObj->bytes_received=intval($line[2]);
      $lineObj->bytes_send=intval($line[3]);
      $status->client_list[]=$lineObj;
    }
    $i+=2;
    for(;$i<count($statusLines);$i++)
    {
      if($statusLines[$i]==='GLOBAL STATS')
        break;
      $line=explode(',',$statusLines[$i]);
      $lineObj=new stdClass;
      $lineObj->local_address=$line[0];
      $lineObj->player_id=intval($line[1]);
      $lineObj->remote_address=$line[2];
      $status->routing_table[]=$lineObj;
    }
    return $status;
  }
}
