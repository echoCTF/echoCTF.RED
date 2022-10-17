<?php
namespace app\modules\sales\commands;

use yii\console\Controller;
use yii\helpers\Console;
use app\modules\sales\models\Product;
use app\modules\sales\models\PlayerSubscription;
use app\modules\sales\models\PlayerCustomerSearch as PlayerCustomer;

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
    public function actionExpireSubscriptions()
    {
      $playerSubs=PlayerSubscription::find()->active()->andWhere(['<','ending',new \yii\db\Expression('NOW()-INTERVAL 4 HOUR')]);
      foreach($playerSubs->all() as $rec)
      {
        $transaction = \Yii::$app->db->beginTransaction();
        try
        {
          printf("Expiring: %s => %s\n", $rec->player->email,$rec->subscription_id);
          if($rec->product)
          {
            \Yii::$app->db->createCommand("DELETE FROM network_player WHERE player_id=:player_id AND network_id IN (SELECT network_id FROM product_network WHERE product_id=:product_id)")
            ->bindValue(':player_id',$rec->player_id)
            ->bindValue(':product_id',$rec->product->id)
            ->execute();
            $rec->updateAttributes(['active'=>0]);
          }
          else
          {
            \Yii::$app->db->createCommand("DELETE FROM network_player WHERE player_id=:player_id")
            ->bindValue(':player_id',$rec->player_id)
            ->execute();
            $rec->delete();
          }
          if($rec->player->last->vpn_local_address!==null)
          {
            // TODO: Pf::kill tables from networks and instances that this user had
            // Pf::kill_table($val->target->name.'_'.$val->player_id,true);
            // Pf::kill_table($val->target->name.'_'.$val->player_id.'_clients',true);
          }
          $transaction->commit();
        }
        catch (\Throwable $e)
        {
          $transaction->rollBack();
          echo "Rolling back: ",$e->getMessage(),"\n";
          throw $e;
        }
      }
    }

    /**
     * Fetch stripe customers details
     */
    public function actionStripeImport()
    {
      echo "Importing Player Customers\n";
      PlayerCustomer::FetchStripe();
      echo "Importing Products\n";
      Product::FetchStripe();
      echo "Importing Player subscriptions\n";
      PlayerSubscription::FetchStripe();
    }
}
