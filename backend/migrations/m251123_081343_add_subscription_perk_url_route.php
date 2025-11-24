<?php

use yii\db\Migration;

class m251123_081343_add_subscription_perk_url_route extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->upsert('url_route', ['source' => 'subscription/perk/configure/<id>', 'destination' => 'subscription/perk/configure', 'weight' => 725], true);
    $this->upsert('url_route', ['source' => 'subscription/payments', 'destination' => 'subscription/default/payments', 'weight' => 726], true);
    $this->upsert('url_route', ['source' => 'subscription/payment/<id>', 'destination' => 'subscription/default/payment', 'weight' => 727], true);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    echo "m251123_081343_add_subscription_perk_url_route cannot be reverted.\n";
  }
}
