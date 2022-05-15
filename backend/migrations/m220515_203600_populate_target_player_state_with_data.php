<?php

use yii\db\Migration;

/**
 * Class m220515_203600_populate_target_player_state_with_data
 */
class m220515_203600_populate_target_player_state_with_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("INSERT INTO target_player_state (id,player_id,player_points,player_treasures) select target_id,player_id,sum(t1.points),count(*) from player_treasure as t1 left join treasure as t2 on t1.treasure_id=t2.id group by target_id,player_id")->execute();
        $this->db->createCommand("INSERT INTO target_player_state (id,player_id,player_points,player_findings) select target_id,player_id,sum(t1.points),count(*) from player_finding as t1 left join finding as t2 on t1.finding_id=t2.id group by target_id,player_id ON DUPLICATE KEY UPDATE player_findings=values(player_findings),player_points=player_points+values(player_points)")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220515_203600_populate_target_player_state_with_data cannot be reverted.\n";

    }
}
