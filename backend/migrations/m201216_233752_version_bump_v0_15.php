<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Class m201216_233752_version_bump_v0_15
 */
class m201216_233752_version_bump_v0_15 extends Migration
{
  public function safeUp()
  {
    $this->update('sysconfig', ['val'=>'v0.15'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.13","v0.15")')], ['id' => 'frontpage_scenario']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->update('sysconfig', ['val'=>'v0.13'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.15","v0.13")')], ['id' => 'frontpage_scenario']);
  }
}
