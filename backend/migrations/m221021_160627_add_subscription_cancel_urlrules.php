<?php

use yii\db\Migration;

/**
 * Class m221021_160627_add_subscription_cancel_urlrules
 */
class m221021_160627_add_subscription_cancel_urlrules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->upsert('url_route',['source'=>'subscription/cancel','destination'=>'subscription/default/cancel-subscription','weight'=>705]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('url_route',['source'=>'subscription/cancel','destination'=>'subscription/default/cancel-subscription']);
    }
}
