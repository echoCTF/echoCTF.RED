<?php

namespace app\modules\subscription\models;

use Yii;
use \app\models\Player;
/**
 * This is the class for wrapping customer operations.
 *
 * @property string|null $subscription_id
 * @property string|null $name
 * @property string|null $metadata
 */
class Customer extends \yii\base\Model
{

    public $object;
    public $player_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    public static function setCustomerId()
    {

    }
    public static function getCustomerId()
    {
      if(empty(Yii::$app->user->identity->stripe_customer_id) && self::existsOnStripe()===false)
      {
        self::createOnStripe();
      }
      return Yii::$app->user->identity->stripe_customer_id;

    }

    public static function createOnStripe()
    {
      $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
      $stripe_customer = $stripe->customers->create([
        'description' => Yii::$app->user->identity->username,
        'email' => Yii::$app->user->identity->email,
        'name' => Yii::$app->user->identity->fullname,
        'metadata'=> ['player_id'=>Yii::$app->user->id,'profile_id'=>Yii::$app->user->identity->profile->id,'twitter'=>Yii::$app->user->identity->profile->twitter]
      ]);
      Yii::$app->user->identity->updateAttributes(['stripe_customer_id'=>$stripe_customer->id]);
      return $stripe_customer;
    }

    public static function createFromStripe($object)
    {
      $player=Player::findByEmail($object->email);
      if($player)
      {
        $player->updateAttributes(['stripe_customer_id'=>$object->id]);
        $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
        $stripe->customers->update(
          $object->id,
          [
            'metadata'=> [
              'player_id'=>$player->id,
              'profile_id'=>$player->profile->id,
              'twitter'=>$player->profile->twitter
              ]
          ]
        );
      }
      return $player;
    }

    public static function existsOnStripe()
    {
      $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
      $stripe_customer=null;
      try
      {
        if(Yii::$app->user->identity->stripe_customer_id!="")
          $stripe_customer = $stripe->customers->retrieve(Yii::$app->user->identity->stripe_customer_id,[]);
        else
        {
          $scus = $stripe->customers->all(['email'=>Yii::$app->user->identity->email,'limit'=>1]);
          if(count($scus->data)>0)
            $stripe_customer = $scus->data[0];
        }
        if($stripe_customer!=null && $stripe_customer->deleted) throw new \Exception(\Yii::t('app','Deleted stripe customer'));
      }
      catch (\Exception $e)
      {
        if(Yii::$app->user->identity->stripe_customer_id!="")
          Yii::$app->user->identity->updateAttributes(['stripe_customer_id'=>null]);
        return false;
      }
      if($stripe_customer==null) return false;
      Yii::$app->user->identity->updateAttributes(['stripe_customer_id'=>$stripe_customer->id]);
      return true;
    }


}
