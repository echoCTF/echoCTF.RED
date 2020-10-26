<?php

use yii\db\Migration;

/**
 * Class m201026_110049_add_approved_avatar_to_sysconfig
 */
class m201026_110049_add_approved_avatar_to_sysconfig extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("INSERT INTO sysconfig (id,val) VALUES ('approved_avatar','1') ON DUPLICATE KEY UPDATE val=values(val)")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
