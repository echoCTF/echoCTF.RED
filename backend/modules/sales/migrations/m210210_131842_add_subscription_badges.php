<?php

use yii\db\Migration;

/**
 * Class m210210_131842_add_subscription_badges
 */
class m210210_131842_add_subscription_badges extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->insert('badge',[
        'id' => 9,
        'name' => 'VIP Player',
        'pubname' => '<img src="/images/badges/vip-card.svg">',
        'description' => 'This badge is to thank you for supporting us with a subscription purchase. Words cannot express our gratitude for your support. Thank you!!!',
        'pubdescription' => 'This user has supported us with a subscription purchase. Words cannot express our gratitude for the support.',
        'points' => 0,
      ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('badge',['id' => 9]);
    }
}
