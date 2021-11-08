<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Class m211108_105742_version_bump_v0_18
 */
class m211108_105742_version_bump_v0_18 extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->update('sysconfig', ['val'=>'v0.18'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.17","v0.18")')], ['id' => 'frontpage_scenario']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->update('sysconfig', ['val'=>'v0.17'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.18","v0.17")')], ['id' => 'frontpage_scenario']);
  }

}
