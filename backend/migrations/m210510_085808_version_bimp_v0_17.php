<?php

use yii\db\Migration;
use yii\db\Expression;
/**
 * Class m210510_085808_version_bimp_v0_17
 */
class m210510_085808_version_bimp_v0_17 extends Migration
{
  public function safeUp()
  {
    $this->db->createCommand("INSERT INTO sysconfig (id,val) values ('platform_version','v0.17') ON DUPLICATE KEY UPDATE val=values(val)")->execute();
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.16","v0.17")')], ['id' => 'frontpage_scenario']);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->db->createCommand("INSERT INTO sysconfig (id,val) values ('platform_version','v0.16') ON DUPLICATE KEY UPDATE val=values(val)")->execute();
    $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.17","v0.16")')], ['id' => 'frontpage_scenario']);
  }
}
