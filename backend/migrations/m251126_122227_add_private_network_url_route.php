<?php

use yii\db\Migration;

class m251126_122227_add_private_network_url_route extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->upsert('url_route', ['source' => 'network/private/<id:\d+>', 'destination' => 'network/private/view', 'weight' => 631], true);
    $this->upsert('url_route', ['source' => 'network/private/spin/<network_id:\d+>/<target_id:\d+>', 'destination' => 'network/private/spin', 'weight' => 632], true);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    echo "m251126_122227_add_private_network_url_route cannot be reverted.\n";
  }
}
