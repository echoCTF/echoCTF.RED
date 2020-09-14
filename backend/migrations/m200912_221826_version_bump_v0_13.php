<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Class m200912_221826_version_bump_v0_13
 */
class m200912_221826_version_bump_v0_13 extends Migration
{
  public function safeUp()
  {
    $this->update('sysconfig', ['val'=>'v0.13'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.12","v0.13")')], ['id' => 'frontpage_scenario']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->update('sysconfig', ['val'=>'v0.12'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.13","v0.12")')], ['id' => 'frontpage_scenario']);
  }
}
