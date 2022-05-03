<?php

use yii\db\Migration;

/**
 * Class m220425_221754_version_bump_v0_20_0
 */
class m220425_221754_version_bump_v0_20_0 extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->update('sysconfig', ['val'=>'v0.20.0'], ['id'=>'platform_version']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->update('sysconfig', ['val'=>'v0.19.0'], ['id'=>'platform_version']);
  }

}
