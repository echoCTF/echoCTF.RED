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
  public function actionExpireSubscriptions($active = false, $interval = 1440)
  {
    if (boolval($active) === true)
      $playerSubs = PlayerSubscription::find()->active(intval($active))->expired($interval);
    else
      $playerSubs = PlayerSubscription::find()->expired($interval);

    foreach ($playerSubs->all() as $rec) {
      $transaction = \Yii::$app->db->beginTransaction();
      try {
        if ($rec->active)
          printf("Expiring: %s %s => %s / %s\n", $rec->player->username, $rec->player->email, $rec->subscription_id,\Yii::$app->formatter->asRelativeTime($rec->ending));
        else
          printf("Cleaning: %s %s => %s / %s\n", $rec->player->username, $rec->player->email, $rec->subscription_id,$rec->ending);
        if ($rec->product) {
          $notif=new \app\modules\activity\models\Notification;
          $notif->player_id=$rec->player_id;
          $notif->category='swal:info';
          $notif->title=\Yii::t('app','Your subscription has expired');
          $notif->body= \Yii::t('app','We\'re sorry to let you know that your '.$rec->product->name.' subscription has expired. Feel free to re-subscribe at any time.');
          $notif->archived=0;
          $rec->cancel();
          $notif->save();
        } else {
          \Yii::$app->db->createCommand("DELETE FROM network_player WHERE player_id=:player_id")
            ->bindValue(':player_id', $rec->player_id)
            ->execute();
        }
        // notify user
        $rec->delete();
        $transaction->commit();
      } catch (\Throwable $e) {
        $transaction->rollBack();
        echo "Rolling back: ", $e->getMessage(), "\n";
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

  /**
   * Fetch stripe customers details
   */
  public function actionStripeImportProducts()
  {
    echo "Importing Products\n";
    Product::FetchStripe();
  }

  /**
   * Delete Expired subscriptions
   */
  public function actionDeleteInactive()
  {
    $transaction = \Yii::$app->db->beginTransaction();
    try {
      echo "Deleting expired subscriptions\n";
      echo "Deleted ".PlayerSubscription::DeleteInactive()." subscriptions\n";
      $transaction->commit();
    }
    catch (\Exception $e) {
      $transaction->rollBack();
      echo "Failed to delete inactive subscriptions ".$e->getMessage()."\n";
    }
  }
}
