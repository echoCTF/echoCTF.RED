<?php

use yii\db\Migration;

/**
 * Class m201212_160729_update_player_finding_points
 */
class m201212_160729_update_player_finding_points extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp()
     {
       $cmd="UPDATE player_finding as t1 LEFT JOIN finding as t2 on t1.finding_id=t2.id SET t1.points=t2.points";
       $this->db->createCommand($cmd)->execute();
     }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201212_160729_update_player_finding_points cannot be reverted.\n";
    }
}
