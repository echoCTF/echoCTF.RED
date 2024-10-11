<?php

use yii\db\Migration;

/**
 * Class m240918_062559_add_email_unique_index
 */
class m240918_062559_add_email_unique_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand('alter table player drop index if exists player_email_idx')->execute();
      $this->createIndex ( 'player_email_idx', 'player', 'email', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropIndex ( 'player_email_idx', 'player');
    }
}
