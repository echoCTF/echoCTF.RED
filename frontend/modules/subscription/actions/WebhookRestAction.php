<?php

namespace app\modules\subscription\actions;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\db\Expression;

use app\models\Player;
use app\models\Notification;
use app\modules\subscription\models\PlayerSubscription;
use app\modules\subscription\models\Subscription;
use app\modules\subscription\models\Customer;
use app\modules\subscription\models\Price;
use app\modules\subscription\models\PlayerProduct;
use yii\base\UserException;

/**
 * REST action to handle the Webhook events from Stripe
 */
class WebhookRestAction extends \yii\rest\Action
{
  public $modelClass = "\app\modules\subscription\models\PlayerSubscription";
  public $serializer = 'yii\rest\Serializer';

  public function run()
  {

    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);
    \Stripe\Stripe::setEnableTelemetry(false);

    $webhookSecret = \Yii::$app->sys->stripe_webhookSecret;
    if ($webhookSecret) {
      try {
        // Parse the message body and check the signature
        $event = \Stripe\Webhook::constructEvent(
          Yii::$app->request->getRawBody(),
          Yii::$app->request->headers->get('stripe-signature'),
          $webhookSecret
        );
      } catch (\Exception $e) {
        \Yii::$app->response->statusCode = 403;
        Yii::error($e);
        return ['error' => $e->getMessage()];
      }
    }
    $type = $event['type'];
    $object = $event['data']['object'];
    \Yii::$app->db->createCommand()->insert('stripe_webhook', [
      'type' => $type,
      'object' => $object,
      'object_id' => $object->id
    ])->execute();

    switch ($type) {
      // message received when subscription created, renewed or canceled
      case 'customer.subscription.updated':
        $player = Player::findOne(['stripe_customer_id' => $object->customer]);
        if ($player === null) {
          \Yii::$app->response->statusCode = 400;
          return ['error' => 'No such customer exists on the platform.'];
        }
        $ps = PlayerSubscription::find()->where(['player_id' => $player->id])->one();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
          if ($ps === null) {
            $ps = new PlayerSubscription;
            $ps->player_id = $player->id;
            $ps->created_at = new \yii\db\Expression("NOW()");
          } else {
            // cancel subscription extras in case new metadata have other networks
            $ps->cancel();
            // If currently there is an active subscription then just
            if ($ps->active == 1 && $object->status !== "active") {
              $ps->updated_at = new \yii\db\Expression("NOW()");
              $ps->active = 0;
              $ps->save();
              $transaction->commit();
              return ['status' => 'success', 'message' => 'canceled subscription'];
            }
          }

          $ps->subscription_id = $object->id;
          $si = $object->items->data[0];
          $starting = $object->current_period_start;
          $ending = $object->current_period_end;
          $ps->starting = new \yii\db\Expression("from_unixtime($starting)");
          $ps->ending = new \yii\db\Expression("from_unixtime($ending)");
          $ps->active = $object->status === "active" ? 1 : 0;
          $ps->price_id = $si->plan->id;
          $ps->updated_at = new \yii\db\Expression("NOW()");
          $ps->save();
          $ps->give();
          $transaction->commit();
        } catch (\Exception $e) {
          $transaction->rollBack();
          \Yii::$app->response->statusCode = 403;
          Yii::error($e);
          return ['error' => $e->getMessage()];
        }
        break;
      /**
       * When a customer is deleted also remove the corresponding id from player
       * (stripe_customer_id)
       */
      case 'customer.deleted':
        $player = Player::findOne(intval($object->metadata->player_id));
        if ($player) {
          Yii::debug($player->updateAttributes(['stripe_customer_id' => null]));
        }
        break;

      /**
       * When a customer is created update the local stripe_customer_id also
       */
      case 'customer.created':
        $transaction = \Yii::$app->db->beginTransaction();
        try {
          Customer::createFromStripe($object);
          $transaction->commit();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
          $transaction->rollBack();
          \Yii::$app->response->statusCode = 403;
          return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
          $transaction->rollBack();
          \Yii::$app->response->statusCode = 403;
          Yii::error($e);
          return ['error' => $e->getMessage()];
        }
        break;
      /**
       * When a subscription is deleted completely then cancel it on our systems
       */
      case 'customer.subscription.deleted':
        $transaction = \Yii::$app->db->beginTransaction();
        try {
          Subscription::cancel($object);
          $transaction->commit();
        } catch (\yii\base\UserException $e) {
          $transaction->rollBack();
          \Yii::$app->response->statusCode = 403;
          Yii::error($e);
          return ['error' => $e->getMessage()];
        }

        break;

      /**
       * Handle product purchases
       */
      case 'checkout.session.completed':
        try {
          if ($object->metadata === [] || !isset($object->metadata->price_id) || !isset($object->metadata->player_id)) {
            throw new UserException('Metadata price_id, player_id and price_id not provided!!!');
          }
          $price = Price::findOne($object->metadata->price_id);
          $product = $price->product;
          $player = Player::findOne($object->metadata->player_id);
          if ($object->mode == 'payment' && $object->payment_status == 'paid' && $player !== null && $price !== null && $price->ptype === 'one_time') {
            if (isset($price->metadataObj->days) && intval($price->metadataObj->days) > 0) {
              $pp = new PlayerProduct();
              $pp->player_id = $player->id;
              $pp->product_id = $product->id;
              $pp->price_id = $price->id;
              $pp->metadata = json_encode($object->metadata);
              $pp->ending = new Expression('NOW()+interval :x day', ['x' => intval($price->metadataObj->days)]);
              $pp->save();
            }

            if (isset($price->metadataObj->spins)) {
              $psp = $player->profile->spins;
              if (intval($price->metadataObj->spins) == 0) {
                $psp->setOldAttribute('counter', null);
                $psp->updateAttributes(['counter' => 0]);
              } else {
                $psp->setOldAttribute('counter', null);
                $psp->updateAttributes(['counter' => 0, 'perday' => new Expression('perday+:extras', ['extras' => intval($price->metadataObj->spins)])]);
              }

              $player->notify(
                isset($price->metadataObj->notification_type) ? $price->metadataObj->notification_type : 'swal:success', // type
                isset($price->metadataObj->notification_title) ? $price->metadataObj->notification_title : $product->name . ' activated', //title
                isset($price->metadataObj->notification_body) ? $price->metadataObj->notification_body : 'Your ' . $product->name . ' have been activated. Thank you for your support!!!', // body
              );
            }
          }
        } catch (\Exception $e) {
          \Yii::$app->response->statusCode = 403;
          \Yii::error($e);
          return ['error' => $e->getMessage()];
        }
        break;

      case 'payment_intent.succeeded':
        if ($object->data === []) {
          throw new UserException('Empty data[]');
        }
        $data = $object->charges->data[0];
        if (isset($data->paid) && $data->paid === true) {
          try {
            $player = Player::findOne(['stripe_customer_id' => $object->customer]);
            if ($player !== null)
              Yii::$app->db->createCommand()->insert('player_payment_history', [
                'payment_id' => $object->id,
                'player_id' => $player->id,
                'amount' => $object->amount,
                'metadata' => yii\helpers\Json::encode($object, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'created_at' => new Expression('NOW()'),
              ])->execute();
          } catch (\Exception $e) {
          }
        }

        break;

      default:
        // Unhandled event type
    }

    return ['status' => 'success'];
  }
}
