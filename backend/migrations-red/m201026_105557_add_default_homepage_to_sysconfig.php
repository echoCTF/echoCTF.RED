<?php

use yii\db\Migration;

/**
 * Class m201026_105557_add_default_homepage_to_sysconfig
 */
class m201026_105557_add_default_homepage_to_sysconfig extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("INSERT INTO sysconfig (id,val) VALUES ('default_homepage','/dashboard/index') ON DUPLICATE KEY UPDATE val=values(val)")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

}
