<?php

use yii\db\Migration;

/**
 * Class m191024_133206_populate_new_profile_table
 */
class m191024_133206_populate_new_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("INSERT INTO {{%profile}} ({{%player_id}},{{%visibility}}) SELECT {{%id}},'private' FROM {{%player}}")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->db->createCommand()->truncateTable('{{%profile}}')->execute();

    }

}
