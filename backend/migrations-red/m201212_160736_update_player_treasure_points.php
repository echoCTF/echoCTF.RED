<?php

use yii\db\Migration;

/**
 * Class m201212_160736_update_player_treasure_points
 */
class m201212_160736_update_player_treasure_points extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $cmd="UPDATE player_treasure as t1 LEFT JOIN treasure as t2 on t1.treasure_id=t2.id SET t1.points=t2.points";
      $this->db->createCommand($cmd)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201212_160736_update_player_treasure_points cannot be reverted.\n";
    }
}
