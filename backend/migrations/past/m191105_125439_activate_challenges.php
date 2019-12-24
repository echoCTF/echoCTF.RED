<?php

use yii\db\Migration;

/**
 * Class m191105_125439_activate_challenges
 */
class m191105_125439_activate_challenges extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->delete('{{%disabled_route}}', ['route' => 'challenge%']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->insert('{{%disabled_route}}', ['route' => 'challenge%']);
    }
}
