<?php

namespace app\modules\sales\commands;

use yii\console\Controller;
use yii\helpers\Console;
use app\modules\sales\models\Product;
use app\modules\sales\models\PlayerProduct;
use app\modules\sales\models\PlayerSubscription;
use app\modules\sales\models\PlayerPaymentHistory;
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
    $playerSubs = PlayerSubscription::find()->active(intval($active))->expired($interval);

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

          if ($rec->product && $rec->active) {
            $p=$rec->player;
            $rec->cancel();
            $p->notify('swal:info',\Yii::t('app', 'Your subscription has expired'),\Yii::t('app', 'We\'re sorry to let you know that your ' . $rec->product->name . ' subscription has expired. Feel free to re-subscribe at any time.'));
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
    echo "Importing Payments\n";
    PlayerPaymentHistory::FetchStripePayments();
    echo "Importing Refunds\n";
    PlayerPaymentHistory::FetchStripeRefunds();
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
  /**
   * Expire Player Products taking care of cases where instances
   * or private networks are assigned to them.
   */
  public function actionExpiredPlayerProducts()
  {
    foreach (PlayerProduct::find()->expired()->all() as $pp) {
      $notification_title = \Yii::t('app', '{product_name} expired', ['product_name' => $pp->product->name]);
      $notification_body = \Yii::t('app', 'Your {product_name} has expired. Feel free to get a new one any time. Thank you for your support!', ['product_name' => $pp->product->name]);
      $player = $pp->player; // Get a copy of the player model

      if (isset($pp->metadataObj->private_network_id) && ($pn = \app\modules\infrastructure\models\PrivateNetwork::findOne(intval($pp->metadataObj->private_network_id))) !== null) {
        $updatedRecords = \app\modules\infrastructure\models\PrivateNetworkTarget::updateAll(['state' => 2], [
          'and',
          ['!=', 'state', 2],
          ['is not', 'server_id', null],
          ['=', 'private_network_id', intval($pp->metadataObj->private_network_id)]
        ]);
        $totalRecords = \app\modules\infrastructure\models\PrivateNetworkTarget::find()->andWhere(['state' => 2, 'server_id' => null, 'private_network_id' => intval($pp->metadataObj->private_network_id)])->count();

        if ($totalRecords === count($pn->privateTargets)) {
          if ($pp->delete() && $pn->delete()) // Delete the player product
            $player->notify('swal:info', $notification_title, $notification_body); // notify player
          else
            throw new UserException('Failed to delete Player Product or Private Network.');
        }
      } else {
        if ($pp->delete()) // Delete the player product
          $player->notify('swal:info', $notification_title, $notification_body); // notify player
        else
          throw new UserException('Failed to delete Player Product.');
      }
    }
  }

  /**
   * Retrieve all payments from Stripe
   */
  public function actionStripeImportPayments()
  {
    $transaction = \Yii::$app->db->beginTransaction();
    try {
      echo "Importing Payments\n";
      PlayerPaymentHistory::FetchStripePayments();
      PlayerPaymentHistory::FetchStripeRefunds();
      $transaction->commit();
    } catch (\Exception $e) {
      $transaction->rollBack();
      echo "Failed to import payments history: " . $e->getMessage() . "\n";
    }
  }
  /**
   * Retrieve all refunds from Stripe
   */
  public function actionStripeImportRefunds()
  {
    $transaction = \Yii::$app->db->beginTransaction();
    try {
      echo "Importing Refunds\n";
      PlayerPaymentHistory::FetchStripeRefunds();
      $transaction->commit();
    } catch (\Exception $e) {
      $transaction->rollBack();
      echo "Failed to import refunds: " . $e->getMessage() . "\n";
    }
  }
}
