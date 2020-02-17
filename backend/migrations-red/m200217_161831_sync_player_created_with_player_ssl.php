<?php

use yii\db\Migration;

/**
 * Class m200217_161831_sync_player_created_with_player_ssl
 */
class m200217_161831_sync_player_created_with_player_ssl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("SET @TRIGGER_CHECKS=false")->execute();
      $this->db->createCommand("update player as t1 left join player_ssl as t2 on t2.player_id=t1.id SET t1.created=t2.ts WHERE t1.created is null")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

}
