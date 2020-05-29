<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Class m200529_143337_version_bump_v0_11
 */
class m200529_143337_version_bump_v0_11 extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->update('sysconfig', ['val'=>'v0.11'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.10","v0.11")')], ['id' => 'frontpage_scenario']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->update('sysconfig', ['val'=>'v0.10'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.11","v0.10")')], ['id' => 'frontpage_scenario']);
  }
}
