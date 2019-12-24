<?php

use yii\db\Migration;

/**
 * Class m191107_134713_add_target_suggested_xp_index
 */
class m191107_134713_add_target_suggested_xp_index extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createIndex(
        '{{%idx-target-suggested_xp}}',
        '{{%target}}',
        'suggested_xp'
    );

  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropIndex(
      '{{%idx-target-suggested_xp}}',
      '{{%target}}'
    );
  }
}
