<?php

namespace app\modules\sales\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "player_subscription".
 *
 * @property int $player_id
 * @property string|null $subscription_id
 * @property string|null $session_id
 * @property string|null $price_id
 * @property int|null $active
 * @property timestamp|null $starting
 * @property timestamp|null $ending
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class PlayerSubscription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_subscription';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'required'],
            [['player_id', 'active'], 'integer'],
            [['created_at', 'updated_at','starting','ending'], 'safe'],
            [['subscription_id', 'session_id', 'price_id'], 'string', 'max' => 255],
            [['player_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => Yii::t('app', 'Player ID'),
            'subscription_id' => Yii::t('app', 'Subscription ID'),
            'session_id' => Yii::t('app', 'Session ID'),
            'price_id' => Yii::t('app', 'Price ID'),
            'active' => Yii::t('app', 'Active'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|PlayerQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery|ProductQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['price_id' => 'price_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSubscriptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerSubscriptionQuery(get_called_class());
    }

    /**
     * Gets all Product from Stripe and merges with existing ones (if any).
     * @return mixed
     */
    public static function FetchStripe()
    {
      $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
      $stripeSubs=$stripe->subscriptions->all([]);
      foreach($stripeSubs->data as $stripe_subscription)
      {
        $player=Player::findOne(['stripe_customer_id'=>$stripe_subscription->customer]);
        if($player!==null)
        {
          if(($ps=PlayerSubscription::findOne($player->id))===null)
          {
            $ps=new PlayerSubscription;
            $ps->player_id=$player->id;
          }
          $ps->subscription_id=$stripe_subscription->id;
          $ps->starting=new \yii\db\Expression("FROM_UNIXTIME(:starting)",[':starting'=>$stripe_subscription->current_period_start]);
          $ps->ending=new \yii\db\Expression("FROM_UNIXTIME(:ending)",[':ending'=>$stripe_subscription->current_period_end]);
          $ps->created_at=new \yii\db\Expression("FROM_UNIXTIME(:ts)",[':ts'=>$stripe_subscription->created]);
          $ps->updated_at=new \yii\db\Expression('NOW()');
          $ps->price_id=$stripe_subscription->items->data[0]->plan->id;
          $ps->active=intval($stripe_subscription->items->data[0]->plan->active);
          if(!$ps->save())
          {
            if(\Yii::$app instanceof \yii\console\Application)
              printf("Failed to save subscription: %s\n",$stripe_subscription->id);
            else
              \Yii::$app->session->addFlash('error', sprintf('Failed to save subscription: %s',$stripe_subscription->id));
          }
          else
          {
            $ps->refresh();
            $sql="INSERT IGNORE INTO network_player (network_id,player_id,created_at,updated_at) SELECT network_id,:player_id,now(),now() FROM product_network WHERE product_id=:product_id";
            \Yii::$app->db->createCommand($sql)
            ->bindValue(':player_id',$player->id)
            ->bindValue(':product_id',$ps->product->id)
            ->execute();
            $metadata=json_decode($ps->product->metadata);
            if(isset($metadata->spins) && intval($metadata->spins)>0)
            {
              $player->playerSpin->updateAttributes(['perday'=>intval($metadata->spins),'counter'=>0]);
            }
            else
            {
              $player->playerSpin->updateAttributes(['counter'=>0]);
            }
            if(\Yii::$app instanceof \yii\console\Application)
              printf("Imported subscription: %s for player %s\n",$stripe_subscription->id,$player->username);
            else
              \Yii::$app->session->addFlash('success', sprintf('Imported subscription: %s for player %s',$stripe_subscription->id,$player->username));
          }
        }
      }
    }
}
