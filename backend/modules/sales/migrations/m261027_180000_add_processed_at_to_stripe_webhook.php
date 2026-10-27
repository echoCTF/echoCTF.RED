<?php

use yii\db\Migration;

/**
 * Remember which Stripe events have been applied, so that a repeated
 * delivery of the same purchase doesn't give the perks twice.
 */
class m261027_180000_add_processed_at_to_stripe_webhook extends Migration
{
  public function safeUp()
  {
    $this->addColumn('{{%stripe_webhook}}', 'processed_at', $this->dateTime()->null()->defaultValue(null));
    $this->createIndex('idx-stripe_webhook-type-object_id', '{{%stripe_webhook}}', ['type', 'object_id']);
  }

  public function safeDown()
  {
    $this->dropIndex('idx-stripe_webhook-type-object_id', '{{%stripe_webhook}}');
    $this->dropColumn('{{%stripe_webhook}}', 'processed_at');
  }
}
