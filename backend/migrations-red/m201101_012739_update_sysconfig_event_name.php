<?php

use yii\db\Migration;

/**
 * Class m201101_012739_update_sysconfig_event_name
 */
class m201101_012739_update_sysconfig_event_name extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->update("sysconfig", ['val'=>'echoCTF.RED'], ['id'=>'event_name']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
      return true;
  }
}
