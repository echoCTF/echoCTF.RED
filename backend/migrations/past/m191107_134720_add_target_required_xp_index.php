<?php

use yii\db\Migration;

/**
 * Class m191107_134720_add_target_required_xp_index
 */
class m191107_134720_add_target_required_xp_index extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createIndex(
        '{{%idx-target-required_xp}}',
        '{{%target}}',
        'required_xp'
    );

  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropIndex(
      '{{%idx-target-required_xp}}',
      '{{%target}}'
    );
  }
}
