<?php

namespace app\modules\subscription\models;

use Yii;
use \app\models\Player;
use \app\modules\network\models\NetworkPlayer;
use app\models\Notification;

/**
 * This is the model class for table "player_subscription".
 *
 * @property int $player_id
 * @property string|null $subscription_id
 * @property string|null $session_id
 * @property string|null $price_id
 * @property boolean|null $active
 * @property datetime|null $starting
 * @property datetime|null $ending
 * @property datetime|null $created_at
 * @property datetime|null $update_at
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

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['subscription_id', 'session_id', 'price_id'], 'string'],
      [['player_id'], 'integer'],
      [['active'], 'boolean'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'player_id' => Yii::t('app', 'Player ID'),
      'subscription_id' => Yii::t('app', 'Stripe subscriptionId'),
      'session_id' => Yii::t('app', 'Stripe sessionId'),
      'price_id' => Yii::t('app', 'Stripe priceId'),
      'active' => Yii::t('app', 'Active'),
    ];
  }

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
      if (isset($metadata->spins) && intval($metadata->spins) > 0) {
        $this->player->profile->spins->updateAttributes(['perday' => \Yii::$app->sys->spins_per_day, 'counter' => 0]);
      }
    }
  }

  public function give()
  {
    $metadata = json_decode($this->product->metadata);
    if (isset($metadata->spins) && intval($metadata->spins) > 0) {
      $this->player->profile->spins->updateAttributes(['perday' => intval($metadata->spins), 'counter' => 0]);
    } else {
      $this->player->profile->spins->updateAttributes(['counter' => 0]);
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
  }

  public function beforeSave($insert)
  {
    $curr = self::findOne($this->player_id);
    if ($this->isNewRecord || ($curr->active != $this->active && $this->active == 1)) {
      $notif = new Notification;
      $notif->player_id = $this->player_id;
      $notif->title = \Yii::t('app', 'Your subscription has been activated');
      $notif->body = \Yii::t('app', 'Your subscription has been activated');
      $notif->archived = 0;
      $notif->save();
    }

    return parent::beforeSave($insert);
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
