<?php

namespace app\modules\sales\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\PlayerSpin;
use app\modules\gameplay\models\NetworkPlayer;
use yii\base\UserException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Html;

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
      [['starting'], 'default', 'value' => \Yii::$app->formatter->asDatetime(new \DateTime('NOW'), 'php:Y-m-d H:i:s')],
      [['ending'], 'default', 'value' => \Yii::$app->formatter->asDatetime(new \DateTime('NOW + 10 day'), 'php:Y-m-d H:i:s')],
      [['player_id'], 'required'],
      [['player_id', 'active'], 'integer'],
      [['ending', 'starting'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
      [['subscription_id', 'session_id', 'price_id'], 'string', 'max' => 255],
      [['player_id'], 'unique'],
      [['created_at', 'updated_at'], 'safe'],
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
   * Deletes inactive subscriptions and ensure their perks have been withdrawn
   * @return int — the number of rows deleted
   * @throws NotSupportedException — if not overridden.
   */
  public static function DeleteInactive(): int
  {
    $deleted = 0;
    foreach (PlayerSubscription::find()->active(0)->all() as $sub) {
      $sub->cancel();
      if ($sub->delete())
        $deleted++;
    }

    return $deleted;
  }

  /**
   * Compare current subscription model with Stripe data
   * @return bool - If data are the same or subscription is sub_vip returns true
   * @throws Exception|UserException - When stripe error occurs or data problems
   */
  public function StripeCompare($invalid_returns = false): bool
  {
    if ($this->subscription_id === 'sub_vip')
      return true;

    \Stripe\Stripe::setEnableTelemetry(false);
    $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
    try {
      $stripe_subscription = $stripe->subscriptions->retrieve($this->subscription_id, []);
    } catch (\Stripe\Exception\InvalidRequestException $e) {
      return $invalid_returns;
    }


    if (intval(\Yii::$app->formatter->asTimestamp($this->starting)) !== intval($stripe_subscription->items->data[0]->current_period_start) || intval(\Yii::$app->formatter->asTimestamp($this->ending)) !== intval($stripe_subscription->items->data[0]->current_period_end))
      return false;

    if (intval($this->active) !== intval(self::stripeActive($stripe_subscription)) || $this->price_id != $stripe_subscription->items->data[0]->plan->id)
      return false;

    return true;
  }

  /**
   * Sync current subscription model with Stripe data
   * @return bool - If data are the same or subscription is sub_vip returns true
   * @throws Exception|UserException - When stripe error occurs or data problems
   */
  public function StripeSync()
  {
    if ($this->subscription_id === 'sub_vip')
      return true;

    \Stripe\Stripe::setEnableTelemetry(false);
    $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
    $stripe_subscription = $stripe->subscriptions->retrieve($this->subscription_id, []);
    $this->subscription_id = $stripe_subscription->id;
    if (intval($stripe_subscription->ended_at == null)) {
      $this->starting = new \yii\db\Expression("FROM_UNIXTIME(:starting)", [':starting' => $stripe_subscription->items->data[0]->current_period_start]);
      $this->ending = new \yii\db\Expression("FROM_UNIXTIME(:ending)", [':ending' => $stripe_subscription->items->data[0]->current_period_end]);
      $this->created_at = new \yii\db\Expression("FROM_UNIXTIME(:ts)", [':ts' => $stripe_subscription->items->data[0]->created]);
    }
    $this->updated_at = new \yii\db\Expression('NOW()');
    $this->price_id = $stripe_subscription->items->data[0]->plan->id;
    $this->active = intval(self::stripeActive($stripe_subscription));
    return $this->update(false);
  }

  /**
   * Gets all Player Subscriptions from Stripe and merges with existing ones (if any).
   * @return mixed
   */
  public static function FetchStripe()
  {
    \Stripe\Stripe::setEnableTelemetry(false);
    $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
    $stripeSubs = $stripe->subscriptions->all(['limit' => 100]);
    foreach ($stripeSubs->autoPagingIterator() as $stripe_subscription) {
      $player = Player::findOne(['stripe_customer_id' => $stripe_subscription->customer]);
      if ($player !== null) {
        if (($ps = PlayerSubscription::findOne($player->id)) === null) {
          $ps = new PlayerSubscription;
          $ps->player_id = $player->id;
        }

        $ps->subscription_id = $stripe_subscription->id;
        if ($stripe_subscription->ended_at == null) {
          $ps->starting = new \yii\db\Expression("FROM_UNIXTIME(:starting)", [':starting' => $stripe_subscription->items->data[0]->current_period_start]);
          $ps->ending = new \yii\db\Expression("FROM_UNIXTIME(:ending)", [':ending' => $stripe_subscription->items->data[0]->current_period_end]);
          $ps->created_at = new \yii\db\Expression("FROM_UNIXTIME(:ts)", [':ts' => $stripe_subscription->items->data[0]->created]);
        }
        $ps->updated_at = new \yii\db\Expression('NOW()');
        $ps->price_id = $stripe_subscription->items->data[0]->plan->id;
        $ps->active = intval(self::stripeActive($stripe_subscription));

        if (!$ps->save(false)) {
          if (\Yii::$app instanceof \yii\console\Application)
            printf("Failed to save subscription: %s\n", $stripe_subscription->id);
          else
            \Yii::$app->session->addFlash('error', sprintf('Failed to save subscription: %s', Html::encode($stripe_subscription->id)));
        } else {
          $ps->refresh();
          if (\Yii::$app instanceof \yii\console\Application)
            printf("Imported subscription: %s for player %s\n", $stripe_subscription->id, $player->username);
          else
            \Yii::$app->session->addFlash('success', sprintf('Imported subscription: %s for player %s', Html::encode($stripe_subscription->id), Html::encode($player->username)));
        }
      } else if (\Yii::$app instanceof \yii\console\Application)
        printf("Customer not found: %s\n", $stripe_subscription->customer);
      else
        \Yii::$app->session->addFlash('warning', sprintf('Customer not found: %s', Html::encode($stripe_subscription->customer)));
    }
  }

  /**
   * Checks all existing subscriptions against stripe and updates
   * the active and price_id fields to match Stripe if different
   *
   * @return mixed
   */
  public static function CheckStripe()
  {
    \Stripe\Stripe::setEnableTelemetry(false);
    $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
    foreach (PlayerSubscription::find()->where(['!=', 'subscription_id', 'sub_vip'])->all() as $ps) {
      try {
        $dosave = false;
        $stripe_subscription = $stripe->subscriptions->retrieve($ps->subscription_id, []);

        // Check if subscription active agree with Stripe
        if (intval($ps->active) !== intval(self::stripeActive($stripe_subscription))) {
          \Yii::$app->session->addFlash('warning', \Yii::t('app', 'Syncing <kbd>active</kbd>, not the same with Stripe <kbd>{subscription_id}</kbd>', ['subscription_id' => $ps->subscription_id]));
          $ps->active = intval(self::stripeActive($stripe_subscription));
          $dosave = true;
        }

        // Check if subscription price_id agrees with Stripe
        if ($ps->price_id != $stripe_subscription->items->data[0]->plan->id) {
          \Yii::$app->session->addFlash('warning', \Yii::t('app', 'Syncing <kbd>price_id</kbd>, not the same with Stripe'));
          $ps->price_id = $stripe_subscription->items->data[0]->plan->id;
          $dosave = true;
        }

        // Check if we have something to to fix active agrees with Stripe
        if ($dosave !== true && $ps->update(true, ['price_id', 'active'])) {
          \Yii::$app->session->addFlash('warning', \Yii::t('app', 'Updated subscription <kbd>{subscription_id}</kbd>', ['subscription_id' => $ps->subscription_id]));
        }
      } catch (\Stripe\Exception\InvalidRequestException $e) {
        if (str_starts_with($e->getMessage(), 'No such subscription:')) {
          $ps->cancel();
          if ($ps->delete())
            \Yii::$app->session->addFlash('success', sprintf('Deleted subscription: %s', Html::encode($ps->subscription_id)));
          else
            \Yii::$app->session->addFlash('error', sprintf('Failed to delete subscription: %s', Html::encode($ps->id)));
        }
      }
    }
  }

  /**
   * Whether the stored row was active before a delete
   * @var bool
   */
  private $_wasActive = false;

  /** Paid, or being retried by Stripe after a failed renewal; unpaid, incomplete and ended are not active */
  public static function stripeActive($stripe_subscription): bool
  {
    return in_array($stripe_subscription->status, ['active', 'past_due'], true);
  }

  /** Daily spins the product of the given price gives (`spins` product metadata key). */
  public static function spinsFor($price_id): int
  {
    if ($price_id === null || ($price = Price::findOne($price_id)) === null || $price->product === null) {
      return 0;
    }
    $metadata = json_decode($price->product->metadata);
    return is_object($metadata) && isset($metadata->spins) ? max(0, intval($metadata->spins)) : 0;
  }

  /**
   * Number of target instances the product of the given price adds to the
   * player (`max_target_instances` product metadata key).
   * @param string|null $price_id
   * @return int
   */
  public static function maxInstancesBonusFor($price_id): int
  {
    if ($price_id === null || ($price = Price::findOne($price_id)) === null || $price->product === null) {
      return 0;
    }
    $metadata = json_decode($price->product->metadata);
    if (is_object($metadata) && isset($metadata->max_target_instances)) {
      return max(0, intval($metadata->max_target_instances));
    }
    return 0;
  }

  /**
   * Add or take back the subscription instances when the row becomes
   * active, inactive or changes price. Nothing happens on saves that don't
   * change any of these, so the bonus is never added twice.
   */
  protected function syncMaxInstances($insert, $changedAttributes)
  {
    $activeChanged = array_key_exists('active', $changedAttributes);
    $priceChanged = array_key_exists('price_id', $changedAttributes);
    if (!$insert && !$activeChanged && !$priceChanged) {
      return;
    }
    $wasActive = $insert ? 0 : intval($activeChanged ? $changedAttributes['active'] : $this->active);
    $oldPriceId = (!$insert && $priceChanged) ? $changedAttributes['price_id'] : $this->price_id;
    $oldBonus = $wasActive === 1 ? self::maxInstancesBonusFor($oldPriceId) : 0;
    $newBonus = intval($this->active) === 1 ? self::maxInstancesBonusFor($this->price_id) : 0;
    PlayerSpin::adjustMaxInstances(intval($this->player_id), $newBonus - $oldBonus);
  }

  public function beforeDelete()
  {
    $this->_wasActive = intval($this->getOldAttribute('active')) === 1;
    return parent::beforeDelete();
  }

  public function afterDelete()
  {
    if ($this->_wasActive) {
      PlayerSpin::adjustMaxInstances(intval($this->player_id), -self::maxInstancesBonusFor($this->getAttribute('price_id')));
    }
    parent::afterDelete();
  }

  public function afterSave($insert, $changedAttributes)
  {
    $this->syncMaxInstances($insert, $changedAttributes);
    if ($this->product)
      $metadata = json_decode($this->product->metadata);
    $activeChanged = array_key_exists('active', $changedAttributes);
    $wasActive = !$insert && intval($activeChanged ? $changedAttributes['active'] : $this->active) === 1;
    $priceChanged = !$insert && array_key_exists('price_id', $changedAttributes) && (string) $changedAttributes['price_id'] !== (string) $this->price_id;
    if ($this->active == 1) {
      // Add the spins difference when the subscription starts or changes plan, keeping spins from other purchases
      if (!$wasActive || $priceChanged) {
        $delta = self::spinsFor($this->price_id) - ($wasActive ? self::spinsFor($changedAttributes['price_id']) : 0);
        $this->player->playerSpin->updateAttributes(['perday' => new Expression('GREATEST(:base, perday + :delta)', [':base' => intval(\Yii::$app->sys->spins_per_day), ':delta' => $delta]), 'counter' => 0]);
      }

      if (isset($metadata->badge_ids)) {
        $badge_ids = explode(',', $metadata->badge_ids);
        foreach ($badge_ids as $bid) {
          \Yii::$app->db->createCommand('INSERT IGNORE INTO player_badge (player_id,badge_id) VALUES (:player_id,:badge_id)')
            ->bindValue(':player_id', $this->player_id)
            ->bindValue(':badge_id', $bid)
            ->execute();
        }
      }

      if (isset($metadata->network_ids)) {
        foreach (explode(',', $metadata->network_ids) as $val) {
          if (NetworkPlayer::findOne(['network_id' => $val, 'player_id' => $this->player_id]) === null) {
            $np = new NetworkPlayer;
            $np->player_id = $this->player_id;
            $np->network_id = $val;
            $np->created_at = new \yii\db\Expression('NOW()');
            $np->updated_at = new \yii\db\Expression('NOW()');
            $np->save();
          }
        }
      }

      if (isset($metadata->api_bearer_enable) && boolval($metadata->api_bearer_enable) === true && \Yii::$app->sys->api_bearer_enable === true && \Yii::$app->sys->subscriptions_feature_api===true) {
        if ($this->player->apiToken === null) {
          $pt = new \app\modules\frontend\models\PlayerToken();
          $pt->player_id = $this->player_id;
          $pt->type = 'API';
          $pt->description = 'generated by subscription';
          if (!$pt->validate('token') || !$pt->save()) {
            throw new UserException(\Yii::t('app', 'Failed to generate API token'));
          }
        }
      }

    } else { // subscription expired
      // Take the subscription spins back once, when it stops being active
      if ($wasActive && ($spins = self::spinsFor($priceChanged ? $changedAttributes['price_id'] : $this->price_id)) > 0) {
        $this->player->playerSpin->updateAttributes(['perday' => new Expression('GREATEST(:base, perday - :spins)', [':base' => intval(\Yii::$app->sys->spins_per_day), ':spins' => $spins]), 'counter' => 0]);
      }

      if (isset($metadata->network_ids)) {
        foreach (explode(',', $metadata->network_ids) as $val) {
          if (($np = NetworkPlayer::findOne(['network_id' => $val, 'player_id' => $this->player_id])) !== null) {
            $np->delete();
          }
        }
      }

      if (isset($metadata->api_bearer_enable) && boolval($metadata->api_bearer_enable) === true && \Yii::$app->sys->api_bearer_enable === true && \Yii::$app->sys->subscriptions_feature_api===true && $this->player->apiToken !== null) {
        $this->player->apiToken->delete();
      }

    }

    return parent::afterSave($insert, $changedAttributes);
  }

  /**
   * Cancel the player subscription removing perks and network
   */
  public function cancel()
  {
    if ($this->product !== null) {
      $metadata = json_decode($this->product->metadata);
      if (isset($metadata->network_ids)) {
        NetworkPlayer::deleteAll([
          'and',
          ['player_id' => $this->player_id],
          ['in', 'network_id', explode(',', $metadata->network_ids)]
        ]);
      }
      // Only reset the spins when the stored subscription is still active, so
      // that calling cancel() on an ended subscription (eg. DeleteInactive) is harmless
      if (intval($this->getOldAttribute('active')) === 1 && isset($metadata->spins) && intval($metadata->spins) > 0) {
        $this->player->profile->spins->updateAttributes(['perday' => new Expression('GREATEST(:base, perday - :spins)', [':base' => intval(\Yii::$app->sys->spins_per_day), ':spins' => intval($metadata->spins)]), 'counter' => 0]);
      }
    }
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
    return $this->hasOne(Product::class, ['id' => 'product_id'])->via('price');
  }

  /**
   * Gets query for [[Price]].
   *
   * @return \yii\db\ActiveQuery|PriceQuery
   */
  public function getPrice()
  {
    return $this->hasOne(Price::class, ['id' => 'price_id']);
  }

  /**
   * {@inheritdoc}
   * @return PlayerSubscriptionQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerSubscriptionQuery(get_called_class());
  }
}
