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
    $this->db->createCommand("INSERT INTO sysconfig (id,val) values ('platform_version','v0.15') ON DUPLICATE KEY UPDATE val=values(val)")->execute();
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.13","v0.15")')], ['id' => 'frontpage_scenario']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->db->createCommand("INSERT INTO sysconfig (id,val) values ('platform_version','v0.14') ON DUPLICATE KEY UPDATE val=values(val)")->execute();
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.15","v0.13")')], ['id' => 'frontpage_scenario']);
  }
}
