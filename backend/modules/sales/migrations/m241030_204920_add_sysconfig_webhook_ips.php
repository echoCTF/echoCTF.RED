<?php

use yii\db\Migration;

/**
 * Class m241030_204920_add_sysconfig_webhook_ips
 */
class m241030_204920_add_sysconfig_webhook_ips extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->upsert('sysconfig',['id'=>'stripe_webhook_ips','val'=>'3.18.12.63,3.130.192.231,13.235.14.237,13.235.122.149,18.211.135.69,35.154.171.200,52.15.183.38,54.88.130.119,54.88.130.237,54.187.174.169,54.187.205.235,54.187.216.72,127.0.0.1']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('sysconfig',['id'=>'stripe_webhook_ips']);
    }
}
