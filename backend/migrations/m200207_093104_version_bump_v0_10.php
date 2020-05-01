<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Class m200207_093104_version_bump_v0_10
 */
class m200207_093104_version_bump_v0_10 extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->update('sysconfig', ['val'=>'v0.10'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.9","v0.10")')], ['id' => 'frontpage_scenario']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->update('sysconfig', ['val'=>'v0.9'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.10","v0.9")')], ['id' => 'frontpage_scenario']);
  }

}
