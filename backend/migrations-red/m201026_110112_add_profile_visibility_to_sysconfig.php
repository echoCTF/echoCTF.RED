<?php

use yii\db\Migration;

/**
 * Class m201026_110112_add_profile_visibility_to_sysconfig
 */
class m201026_110112_add_profile_visibility_to_sysconfig extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("INSERT INTO sysconfig (id,val) VALUES ('profile_visibility','public') ON DUPLICATE KEY UPDATE val=values(val)")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
