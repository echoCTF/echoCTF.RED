<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_subscription}}`.
 */
class m210203_233709_create_player_subscription_table extends Migration
{
  public function safeUp()
  {
      $this->createTable('{{%player_subscription}}', [
          'player_id' => $this->integer()->notNull()->append('PRIMARY KEY'),
          'subscription_id'=>$this->string()->defaultValue(null),
          'session_id'=>$this->string()->defaultValue(null),
          'price_id'=>$this->string()->defaultValue(null),
          'active'=>$this->boolean()->defaultValue(0),
          'starting'=>$this->datetime(),
          'ending'=>$this->datetime(),
          'created_at'=>$this->datetime(),
          'updated_at'=>$this->datetime()
      ]);

      $this->createIndex(
          '{{%idx-player_subscription-player_id}}',
          '{{%player_subscription}}',
          'player_id'
      );

      $this->createIndex(
          '{{%idx-player_subscription-subscription_id}}',
          '{{%player_subscription}}',
          'subscription_id'
      );

  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
      $this->dropTable('{{%player_subscription}}');
  }
}
