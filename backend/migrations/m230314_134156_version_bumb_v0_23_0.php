<?php

use yii\db\Migration;

/**
 * Class m230314_134156_version_bumb_v0_23_0
 */
class m230314_134156_version_bumb_v0_23_0 extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->update('sysconfig', ['val'=>'v0.23.0'], ['id'=>'platform_version']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->update('sysconfig', ['val'=>'v0.22.0'], ['id'=>'platform_version']);
  }

}
