<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Class m200615_072016_version_bump_v0_12
 */
class m200615_072016_version_bump_v0_12 extends Migration
{
  public function safeUp()
  {
    $this->update('sysconfig', ['val'=>'v0.12'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.11","v0.12")')], ['id' => 'frontpage_scenario']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->update('sysconfig', ['val'=>'v0.11'], ['id'=>'platform_version']);
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.12","v0.11")')], ['id' => 'frontpage_scenario']);
  }
}
