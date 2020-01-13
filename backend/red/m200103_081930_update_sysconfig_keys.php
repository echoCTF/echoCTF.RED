<?php

use yii\db\Migration;

/**
 * Class m200103_081930_update_sysconfig_keys
 */
class m200103_081930_update_sysconfig_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->update('sysconfig',['val'=>'@web/uploads'],['id'=>'challenge_home']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      echo "m200103_081930_update_sysconfig_keys cannot be reverted.\n";
    }
}
