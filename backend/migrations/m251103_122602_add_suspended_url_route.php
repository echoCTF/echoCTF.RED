<?php

use yii\db\Migration;

class m251103_122602_add_suspended_url_route extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->upsert('url_route', ['source' => 'suspended', 'destination' => 'site/suspended', 'weight' => 600],true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251103_122602_add_suspended_url_route cannot be reverted.\n";
    }

  }
