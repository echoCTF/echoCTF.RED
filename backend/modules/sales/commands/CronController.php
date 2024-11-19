<?php

namespace app\modules\sales\commands;

use yii\console\Controller;
use yii\helpers\Console;
use app\modules\sales\models\Product;
use app\modules\sales\models\PlayerSubscription;
use app\modules\sales\models\PlayerCustomerSearch as PlayerCustomer;
use yii\base\UserException;

/**
 * Perform Stripe/Sales related cron operations.
 */
class CronController extends Controller
{
  /**
   * Find subscriptions that have expired for more than 24 hours and clean them up
   * TODO: Also disconnect from the VPN
   *
   * This operation is supposed to be run on the VPN Server
   */
  public function actionExpireSubscriptions($active = false, $interval = 1440)
  {
    if (boolval($active) === true)
      $playerSubs = PlayerSubscription::find()->active(intval($active))->expired($interval);
    else
      $playerSubs = PlayerSubscription::find()->expired($interval)->orWhere(['active' => 0]);

    foreach ($playerSubs->all() as $rec) {
      $transaction = \Yii::$app->db->beginTransaction();
      try {
        if ($rec->StripeCompare(true) !== true) {
          printf("Stripe do not agree: %s %s => %s\n", $rec->player->username, $rec->player->email, $rec->subscription_id);
          if ($rec->StripeSync() === false) {
            printf("Failed to sync and update: %s %s => %s\n", $rec->player->username, $rec->player->email, $rec->subscription_id);
            throw new UserException(\Yii::t('app', 'Subscription {subscription} not in sync with Stripe!', ['subscription' => $rec->subscription_id]));
          }
        } else {
          if ($rec->active)
            printf("Expiring: %s %s => %s / %s\n", $rec->player->username, $rec->player->email, $rec->subscription_id, \Yii::$app->formatter->asRelativeTime($rec->ending));
          else
            printf("Inactive: %s %s => %s / %s\n", $rec->player->username, $rec->player->email, $rec->subscription_id, \Yii::$app->formatter->asRelativeTime($rec->ending));

          if ($rec->product) {
            $notif = new \app\modules\activity\models\Notification;
            $notif->player_id = $rec->player_id;
            $notif->category = 'swal:info';
            $notif->title = \Yii::t('app', 'Your subscription has expired');
            $notif->body = \Yii::t('app', 'We\'re sorry to let you know that your ' . $rec->product->name . ' subscription has expired. Feel free to re-subscribe at any time.');
            $notif->archived = 0;
            $rec->cancel();
            $notif->save();
          } else {
            \Yii::$app->db->createCommand("DELETE FROM network_player WHERE player_id=:player_id")
              ->bindValue(':player_id', $rec->player_id)
              ->execute();
          }

          $rec->delete();
        }
        $transaction->commit();
      } catch (\Throwable $e) {
        $transaction->rollBack();
        echo "Rolling back: ", $e->getMessage(), "\n";
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
      echo "Deleted " . PlayerSubscription::DeleteInactive() . " subscriptions\n";
      $transaction->commit();
    } catch (\Exception $e) {
      $transaction->rollBack();
      echo "Failed to delete inactive subscriptions " . $e->getMessage() . "\n";
    }
  }
}
