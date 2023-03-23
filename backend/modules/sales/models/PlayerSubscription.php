<?php

namespace app\modules\sales\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\NetworkPlayer;
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

  /**
   * Deletes inactive subscriptions
   * @return int — the number of rows deleted
   * @throws NotSupportedException — if not overridden.
   */
  public static function DeleteInactive()
  {
    foreach (PlayerSubscription::find()->active(0)->all() as $sub) {
      if ($sub->product)
        $metadata = json_decode($sub->product->metadata);
      if (isset($metadata->network_ids)) {
        NetworkPlayer::deleteAll([
          'and',
          ['player_id' => $sub->player_id],
          ['in', 'network_id', explode(',', $metadata->network_ids)]
        ]);
      }
      $sub->delete();
    }
  }

  /**
   * Gets all Product from Stripe and merges with existing ones (if any).
   * @return mixed
   */
  public static function FetchStripe()
  {
    $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
    $stripeSubs = $stripe->subscriptions->all([]);
    foreach ($stripeSubs->data as $stripe_subscription) {
      $player = Player::findOne(['stripe_customer_id' => $stripe_subscription->customer]);
      if ($player !== null) {
        if (($ps = PlayerSubscription::findOne($player->id)) === null) {
          $ps = new PlayerSubscription;
          $ps->player_id = $player->id;
        }
        $ps->subscription_id = $stripe_subscription->id;
        $ps->starting = new \yii\db\Expression("FROM_UNIXTIME(:starting)", [':starting' => $stripe_subscription->current_period_start]);
        $ps->ending = new \yii\db\Expression("FROM_UNIXTIME(:ending)", [':ending' => $stripe_subscription->current_period_end]);
        $ps->created_at = new \yii\db\Expression("FROM_UNIXTIME(:ts)", [':ts' => $stripe_subscription->created]);
        $ps->updated_at = new \yii\db\Expression('NOW()');
        $ps->price_id = $stripe_subscription->items->data[0]->plan->id;
        $ps->active = intval($stripe_subscription->items->data[0]->plan->active);
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
      }
    }
  }

  public function afterSave($insert, $changedAttributes)
  {
    if ($this->product)
      $metadata = json_decode($this->product->metadata);
    if ($this->active == 1) {
      if (isset($metadata->spins) && intval($metadata->spins) > 0) {
        $this->player->playerSpin->updateAttributes(['perday' => intval($metadata->spins), 'counter' => 0]);
      } else {
        $this->player->playerSpin->updateAttributes(['counter' => 0]);
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
    } else {
      if (isset($metadata->network_ids)) {
        foreach (explode(',', $metadata->network_ids) as $val) {
          if (($np = NetworkPlayer::findOne(['network_id' => $val, 'player_id' => $this->player_id])) !== null) {
            $np->delete();
          }
        }
      }
    }

    return parent::afterSave($insert, $changedAttributes);
  }
}
