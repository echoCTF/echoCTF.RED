<?php

use yii\db\Migration;

/**
 * Class m201102_075742_update_challenges_timer
 */
class m201102_075742_update_challenges_timer extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->update("challenge", ['timer'=>0], ['id'=>'1']);
    $this->update("challenge", ['timer'=>0], ['id'=>'2']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
      return true;
  }
}
