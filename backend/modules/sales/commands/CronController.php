<?php
namespace app\modules\sales\commands;

use yii\console\Controller;
use yii\helpers\Console;

use app\modules\sales\models\PlayerSubscription;

/**
 * Perform Stripe/Sales related cron operations.
 */
class CronController extends Controller
{
    /**
     * Find subscriptions that have expired for more than 4 hours and cause
     * expiration and disconnect from the VPN.
     *
     * This operation is supposed to be run on the VPN Server
     */
    public function actionIndex()
    {
      $playerSubs=PlayerSubscription::find()->active()->andWhere(['<','ending',new \yii\db\Expression('NOW()-INTERVAL 4 HOUR')]);
      foreach($playerSubs->all() as $rec)
      {
        $transaction = \Yii::$app->db->beginTransaction();
        try
        {
          printf("Expiring: %s => %s\n", $rec->player->email,$rec->subscription_id);
          \Yii::$app->db->createCommand("DELETE FROM network_player WHERE player_id=:player_id AND network_id IN (SELECT network_id FROM product_network WHERE product_id=:product_id)")
          ->bindValue(':player_id',$rec->player_id)
          ->bindValue(':product_id',$rec->product->id)
          ->execute();
          $rec->updateAttributes(['active'=>0]);
          if($rec->player->last->vpn_local_address!==null)
          {
            self::vpnKill($rec->player_id,$rec->player->last->vpn_local_address);
          }
          $transaction->commit();
        }
        catch (\Throwable $e)
        {
          $transaction->rollBack();
          echo "Rolling back\n";
          //throw $e;
        }
      }
    }

    /**
     * Check the VPN server port based on the range assigned to the user
     * (if connected).
     * @param integer|null $player_ip IP of the player currently
     * @return array [IP,PORT,PASSWORD]
     */
    protected function determineVpn($player_ip)
    {
      $network=($player_ip & ip2long('255.255.0.0'));
      $creds=\Yii::$app->controller->module->params['vpn_ranges'];
      if(array_key_exists(long2ip($network),$creds)!==false)
      {
        return $creds[long2ip($network)];
      }
    }

    /**
     * Kill a player session from VPN
     * @param integer $player_id The ID of the player
     * @param integer|null $player_ip The current VPN IP of the player (if connected)
     */
    protected function vpnKill($player_id,$player_ip)
    {
      try
      {
        list($vpnip,$vpnport,$pass) = self::determineVpn($player_ip);
        $fp = fsockopen($vpnip, $vpnport, $errno, $errstr, 30);
        if (!$fp)
        {
            throw new \Exception("$errstr ($errno)");
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
}
