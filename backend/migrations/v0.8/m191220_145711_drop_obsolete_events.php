<?php

use yii\db\Migration;

/**
 * Class m191220_145711_drop_memcached_sync_event
 */
class m191220_145711_drop_obsolete_events extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("DROP EVENT IF EXISTS {{%memcached_sync}}")->execute();
      $this->db->createCommand("DROP EVENT IF EXISTS {{%ev_player_hint}}")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191220_145711_drop_memcached_sync_event cannot be reverted.\n";

    }

}
