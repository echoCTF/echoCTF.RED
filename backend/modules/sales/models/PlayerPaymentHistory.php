<?php

namespace app\modules\sales\models;

use Yii;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "player_payment_history".
 *
 * @property int $id
 * @property int $player_id
 * @property string $payment_id
 * @property int|null $amount
 * @property string|null $metadata
 * @property string|null $created_at
 */
class PlayerPaymentHistory extends \yii\db\ActiveRecord
{


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_payment_history';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['metadata', 'created_at'], 'default', 'value' => null],
      [['amount'], 'default', 'value' => 0],
      [['player_id', 'payment_id', 'amount'], 'required'],
      [['amount', 'player_id'], 'integer'],
      [['metadata', 'created_at'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'player_id' => Yii::t('app', 'Player ID'),
      'product_id' => Yii::t('app', 'Product ID'),
      'price_id' => Yii::t('app', 'Price ID'),
      'amount' => Yii::t('app', 'Amount'),
      'metadata' => Yii::t('app', 'Metadata'),
      'created_at' => Yii::t('app', 'Created At'),
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
   * {@inheritdoc}
   * @return PlayerPaymentHistoryQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerPaymentHistoryQuery(get_called_class());
  }

  /**
   * Fetch stripe payments
   */
  public static function FetchStripePayments()
  {
    \Stripe\Stripe::setEnableTelemetry(false);
    $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
    $stripe_payments = $stripe->paymentIntents->all([]);
    foreach ($stripe_payments->autoPagingIterator() as $payment) {
      $filter = ['stripe_customer_id' => $payment->customer];
      $player = Player::findOne($filter);

      if ($player !== null) {
        $pph = new PlayerPaymentHistory([
          'player_id' => $player->id,
          'payment_id' => $payment->id,
          'amount' => $payment->amount,
          'metadata' => \yii\helpers\Json::encode($payment, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
          'created_at' => new \yii\db\Expression('FROM_UNIXTIME(:ts)', ['ts' => $payment->created]),
        ]);
        $pph->save();
        if (\Yii::$app instanceof \yii\console\Application)
          printf("Imported payment %s for customer_id: %s for user %s\n", $payment->id, $player->stripe_customer_id, $player->username);
        else
          \Yii::$app->session->addFlash('success', sprintf('Imported payment for customer_id: <b>%s</b> for user <b>%s</b>', Html::encode($player->stripe_customer_id), Html::encode($player->username)));
      }
    }
  }

  /**
   * Fetch stripe refunds
   */
  public static function FetchStripeRefunds()
  {
    \Stripe\Stripe::setEnableTelemetry(false);
    $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
    $stripe_refunds = $stripe->refunds->all([]);
    foreach ($stripe_refunds->autoPagingIterator() as $refund) {
      $pp = PlayerPaymentHistory::findOne(['payment_id' => $refund->payment_intent]);
      if ($pp != null) {
        $pph = new PlayerPaymentHistory([
          'player_id' => $pp->player_id,
          'payment_id' => $refund->id,
          'amount' => - ($refund->amount),
          'metadata' => \yii\helpers\Json::encode($refund, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
          'created_at' => new \yii\db\Expression('FROM_UNIXTIME(:ts)', ['ts' => $refund->created]),
        ]);
        $pph->save();
        if (\Yii::$app instanceof \yii\console\Application)
          printf("Imported refund %s for customer_id: %s for user %s\n", $refund->id, $pp->player->stripe_customer_id, $pp->player->username);
        else
          \Yii::$app->session->addFlash('success', sprintf('Imported refund for customer_id: <b>%s</b> for user <b>%s</b>', Html::encode($pp->player->stripe_customer_id), Html::encode($pp->player->username)));
      }
    }
  }
}
