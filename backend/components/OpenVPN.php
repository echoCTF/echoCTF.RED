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
      $creds = self::determineServer($player_ip);
      $fp = fsockopen($creds->management_ip_octet, $creds->management_port, $errno, $errstr, 30);
      if (!$fp)
      {
        throw new \Exception("Error connecting to {$creds->managemet_ip_octet}:{$creds->management_port} $errstr ($errno)");
      }
      else
      {
        echo "connected to {$creds->managemet_ip_octet}\n";
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
  static public function determineServer(int $player_ip)
  {
    return \app\modules\settings\models\Openvpn::find()->where(['net'=>new \yii\db\Expression(':player_ip & mask',[':player_ip'=>$player_ip])])->one();
  }
}
