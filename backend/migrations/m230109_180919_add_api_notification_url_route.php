<?php

use yii\db\Migration;

/**
 * Class m230109_180919_add_api_notification_url_route
 */
class m230109_180919_add_api_notification_url_route extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->upsert('url_route',['source'=>'api/notification','destination'=>'api/notification/index','weight'=>641]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230109_180919_add_api_notification_url_route cannot be reverted.\n";
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230109_180919_add_api_notification_url_route cannot be reverted.\n";

        return false;
    }
    */
}
