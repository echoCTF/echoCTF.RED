<?php

use yii\db\Migration;

/**
 * Class m211221_132922_version_bimp_v0_19_0
 */
class m211221_132922_version_bump_v0_19_0 extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->update('sysconfig', ['val'=>'v0.19.0'], ['id'=>'platform_version']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->update('sysconfig', ['val'=>'v0.18'], ['id'=>'platform_version']);
  }

}
