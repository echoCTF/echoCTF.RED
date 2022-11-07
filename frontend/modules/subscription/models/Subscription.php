<?php

namespace app\modules\subscription\models;

use Yii;
use \app\models\Player;
use \app\modules\network\models\NetworkPlayer;
use app\models\Notification;

/**
 * This is the model class for table "player_subscription".
 *
 * @property string|null $subscription_id
 * @property string|null $name
 * @property string|null $metadata
 */
class Subscription extends \yii\base\Model
{

    public $subscription_id;
    public $name;
    public $metadata;
    public $player_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscription_id','name','metadata'], 'string'],
            ['player_id', 'exists', 'targetClass' => '\app\models\Player', 'message' => \Yii::t('app','This player does not exist.')],
            [['player_id'],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subscription_id' => Yii::t('app', 'Subscription Id'),
            'name' => Yii::t('app', 'Name'),
            'metadata' => Yii::t('app', 'Metadata'),
        ];
    }

   /**
    * INSERT or UPDATE a player_subscription on when WebhookAction type is
    * checkout.session.completed
    */
    public static function sessionCompleted($object)
    {
      $ps=self::playerSubscription($object->metadata->player_id);

      if($ps->updateAttributes([
              'active' => ($object->payment_status==="paid") ? true : false,
              'session_id' => $object->id,
              'subscription_id' => $object->subscription,
              'updated_at' => new \yii\db\Expression('NOW()'),
      ])<1)
      {
        throw new \yii\base\UserException(\Yii::t('yii','Failed to save session.completed!'));
      }
    }


    public static function invoicePaid($object)
    {
      // If this invoice is not for one of our players, then ignore
      $player=self::isOurs($object->customer);
      if($player===false)
      {
        return false;
      }

      $ps=self::playerSubscription($player->id);
      $paymentdata=$object->lines->data[0];
      $ps->updateAttributes([
              'price_id' => $paymentdata->price->id,
              'active' => true,
              'subscription_id' => $object->subscription,
              'starting' => new \yii\db\Expression('from_unixtime(:start)',['start'=>$paymentdata->period->start]),
              'ending' => new \yii\db\Expression('from_unixtime(:end)',['end'=>$paymentdata->period->end]),
              'updated_at' => new \yii\db\Expression('NOW()'),
      ]);
      $ps->give();
      return true;
    }

    public static function cancel($object)
    {
      $player=self::isOurs($object->customer);
      if($player===false)
      {
        return false;
      }

      if(($ps=PlayerSubscription::findOne(['player_id'=>$player->id,'active'=>1]))!==null)
      {
        $ps->active=0;
        $ps->updated_at=new \yii\db\Expression('NOW()');
        $ps->cancel();
        $notif=new Notification;
        $notif->player_id=$player->id;
        $notif->title='Your subscription has expired';
        $notif->body='Your subscription has expired';
        $notif->archived=0;
        if($notif->save() && $ps->save())
        {
          return true;
        }
        throw new \yii\base\UserException(\Yii::t('app','Failed to cancel player subscription'));
      }
    }

    /**
     * Get PlayerSubscription by PK
     */
    public static function playerSubscription($id=null)
    {
      if(($ps=PlayerSubscription::findOne($id))===null)
      {
        $ps=new PlayerSubscription;
        $ps->player_id=$id;
        $ps->created_at=new \yii\db\Expression('NOW()');
        $ps->save(false);
      }
      return $ps;
    }

    private static function isOurs($stripe_customer_id=null)
    {
      if($stripe_customer_id!==null)
      {
        if(($player=Player::find()->where(['stripe_customer_id'=>$stripe_customer_id])->one())===null)
        {
          return false;
        }
        return $player;
      }
    }
}
