<?php
namespace app\modules\subscription\actions;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

use app\models\Player;
use app\models\Notification;
use app\modules\subscription\models\PlayerSubscription;
use app\modules\subscription\models\Subscription;
use app\modules\subscription\models\Customer;

/**
 * REST action to handle the Webhook events from Stripe
 */
class WebhookRestAction extends \yii\rest\Action
{
  public $modelClass="\app\modules\subscription\models\PlayerSubscription";
  public $serializer='yii\rest\Serializer';

  public function run()
  {

    \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;

    \Stripe\Stripe::setApiKey(\Yii::$app->sys->stripe_apiKey);

    $webhookSecret = \Yii::$app->sys->stripe_webhookSecret;
    if ($webhookSecret)
    {
      try
      {
          // Parse the message body and check the signature
          $event = \Stripe\Webhook::constructEvent(
            Yii::$app->request->getRawBody(),
            Yii::$app->request->headers->get('stripe-signature'),
            $webhookSecret
          );
        }
        catch (\Exception $e)
        {
          \Yii::$app->response->statusCode=403;
          Yii::error($e);
          return [ 'error' => $e->getMessage() ];
        }
    }
    $type = $event['type'];
    $object = $event['data']['object'];
    \Yii::$app->db->createCommand()->insert('stripe_webhook', [
      'type' => $type,
      'object' => $object,
      'object_id'=>$object->id
    ])->execute();

    switch ($type) {
      // message received when subscription created, renewed or canceled
      case 'customer.subscription.updated':
        $player=Player::findOne(['stripe_customer_id'=>$object->customer]);
        if($player===null)
        {
          \Yii::$app->response->statusCode=400;
          return ['error'=>'No such customer exists on the platform.'];
        }
        $ps=PlayerSubscription::find()->where(['player_id'=>$player->id])->one();
        $transaction=\Yii::$app->db->beginTransaction();
        try
        {
          if($ps===null)
          {
            $ps=new PlayerSubscription;
            $ps->player_id=$player->id;
            $ps->created_at=new \yii\db\Expression("NOW()");
          }
          else
          {
            // cancel subscription extras in case new metadata have other networks
            $ps->cancel();
            // If currently there is an active subscription then just
            if($ps->active==1 && $object->status!=="active")
            {
              $ps->updated_at=new \yii\db\Expression("NOW()");
              $ps->active=0;
              $ps->save();
              $transaction->commit();
              return [ 'status' => 'success','message'=>'canceled subscription' ];
            }
          }

          $ps->subscription_id=$object->id;
          $si=$object->items->data[0];
          $starting=$object->current_period_start;
          $ending=$object->current_period_end;
          $ps->starting=new \yii\db\Expression("from_unixtime($starting)");
          $ps->ending=new \yii\db\Expression("from_unixtime($ending)");
          $ps->active=$object->status==="active" ? 1 : 0;
          $ps->price_id=$si->plan->id;
          $ps->updated_at=new \yii\db\Expression("NOW()");
          $ps->save();
          $ps->give();
          $transaction->commit();
        }
        catch (\Exception $e)
        {
          $transaction->rollBack();
          \Yii::$app->response->statusCode=403;
          Yii::error($e);
          return [ 'error' => $e->getMessage() ];
        }
        break;
      /**
       * When a customer is deleted also remove the corresponding id from player
       * (stripe_customer_id)
       */
      case 'customer.deleted':
        $player=Player::findOne(intval($object->metadata->player_id));
        if($player)
        {
          Yii::debug($player->updateAttributes(['stripe_customer_id'=>null]));
        }
        break;

      /**
       * When a customer is created update the local stripe_customer_id also
       */
      case 'customer.created':
        $transaction=\Yii::$app->db->beginTransaction();
        try {
          Customer::createFromStripe($object);
          $transaction->commit();
        }
        catch(\Stripe\Exception\InvalidRequestException $e)
        {
          $transaction->rollBack();
          \Yii::$app->response->statusCode=403;
          return [ 'error' => $e->getMessage() ];

        }
        catch(\Exception $e)
        {
          $transaction->rollBack();
          \Yii::$app->response->statusCode=403;
          Yii::error($e);
          return [ 'error' => $e->getMessage() ];
        }
        break;
      /**
       * When a subscription is deleted completely then cancel it on our systems
       */
      case 'customer.subscription.deleted':
//      case 'invoice.payment_failed':
        $transaction=\Yii::$app->db->beginTransaction();
        try {
          Subscription::cancel($object);
          $transaction->commit();
        }
        catch(\yii\base\UserException $e)
        {
          $transaction->rollBack();
          \Yii::$app->response->statusCode=403;
          Yii::error($e);
          return [ 'error' => $e->getMessage() ];
        }

        break;
      // ... handle other event types
      default:
        // Unhandled event type
    }

    return [ 'status' => 'success' ];
  }
}
