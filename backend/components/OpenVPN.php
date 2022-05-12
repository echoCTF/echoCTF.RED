<?php
namespace app\components;

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
  static public function logout($id)
  {
    Yii::$app->db->createCommand("UPDATE player_last SET vpn_local_address=NULL, vpn_remote_address=NULL WHERE id=:player",[':player'=>$id])->execute();
  }
  /**
   * Kill a player session from VPN
   * @param integer $player_id The ID of the player
   * @param integer|null $player_ip The current VPN IP of the player (if connected)
   */
  static public function kill($player_id,$player_ip)
  {
    try
    {
      list($vpnip,$vpnport,$pass) = self::determineServer($player_ip);
      $fp = fsockopen($vpnip, $vpnport, $errno, $errstr, 30);
      if (!$fp)
      {
        throw new \Exception("Error connecting to $vpnip:$vpnport $errstr ($errno)");
      }
      else
      {
        echo "connected to $vpnip\n";
        fwrite($fp, "$pass\n");
        usleep(250000);
        fwrite($fp, "kill ${player_id}\n");
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
   * @return array [IP,PORT,PASSWORD]
   */
  static public function determineServer($player_ip)
  {
    $network=($player_ip & ip2long('255.255.0.0'));
    $creds=\Yii::$app->params['vpn_ranges'];
    if(array_key_exists(long2ip($network),$creds)!==false)
    {
      return $creds[long2ip($network)];
    }
    throw new \Exception("Error, failed to determine the VPN server");
  }
}
