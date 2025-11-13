<?php

use yii\db\Migration;

class m251112_130505_add_dashboard_news_url_route extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->upsert('url_route', ['source' => 'dashboard/news/<id:\d+>', 'destination' => 'dashboard/news', 'weight' => 115],true);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251112_130505_add_dashboard_news_url_route cannot be reverted.\n";
    }

}
