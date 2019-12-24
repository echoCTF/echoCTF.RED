<?php

use yii\db\Migration;

/**
 * Class m191024_152136_create_sysconfig_spins_per_day_keys
 */
class m191024_152136_create_sysconfig_spins_per_day_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("INSERT INTO {{%sysconfig}} ({{%id}},{{%val}}) VALUES ('spins_per_day','4') ON DUPLICATE KEY UPDATE {{%val}}=4")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->db->createCommand("DELETE FROM {{%sysconfig}} WHERE {{%id}}='spins_per_day'")->execute();
    }

}
