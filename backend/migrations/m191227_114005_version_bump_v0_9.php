<?php

use yii\db\Migration;

/**
 * Class m191228_114005_version_bump_v0_9
 */
class m191228_114005_version_bump_v0_9 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->update('sysconfig',['val'=>'v0.9'],['id'=>'platform_version']);
      $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.8","v0.9")')], ['id' => 'frontpage_scenario']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->update('sysconfig',['val'=>'v0.8'],['id'=>'platform_version']);
      $this->update('sysconfig',['val'=>new Expression('REPLACE(val,"v0.9","v0.8")')], ['id' => 'frontpage_scenario']);
    }

}
