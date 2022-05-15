<?php

use yii\db\Migration;

/**
 * Class m220514_214530_populate_target_state_with_data
 */
class m220514_214530_populate_target_state_with_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Populate the bulk of the data as taken from widget
         */
        $this->db->createCommand("INSERT IGNORE INTO target_state (id,total_treasures,total_findings,player_rating,total_headshots,total_writeups,approved_writeups) SELECT t.id, count(distinct treasure.id) as total_treasures, count(distinct finding.id) as total_findings, round(avg(CASE WHEN headshot.rating > -1 THEN headshot.rating END)) as player_rating,count(distinct headshot.player_id) as total_headshots, count(distinct writeup.id) as total_writeups,count(distinct (case when writeup.approved=1 then writeup.id end)) AS approved_writeups FROM target t LEFT JOIN treasure ON treasure.target_id=t.id LEFT JOIN finding ON finding.target_id=t.id LEFT JOIN headshot ON headshot.target_id=t.id LEFT JOIN writeup ON writeup.target_id=t.id GROUP BY t.id")->execute();
        // set average timer
        $this->db->createCommand("INSERT INTO target_state (id,timer_avg) SELECT target_id,AVG(timer) FROM headshot GROUP BY target_id ON DUPLICATE KEY UPDATE timer_avg=values(timer_avg)")->execute();
        // set treasure points
        $this->db->createCommand("INSERT INTO target_state (id,treasure_points) SELECT target_id,sum(points) FROM treasure GROUP BY target_id ON DUPLICATE KEY UPDATE treasure_points=values(treasure_points)")->execute();
        // set finding points
        $this->db->createCommand("INSERT INTO target_state (id,finding_points) SELECT target_id,sum(points) FROM finding GROUP BY target_id ON DUPLICATE KEY UPDATE finding_points=values(finding_points)")->execute();

        // set the ondemand target status
        $this->db->createCommand("INSERT INTO target_state (id, on_ondemand,ondemand_state) SELECT target_id,1,{{%state}} FROM target_ondemand ON DUPLICATE KEY UPDATE on_ondemand=values(on_ondemand),ondemand_state=values(ondemand_state)")->execute();

        // Set the on_network flags
        $this->db->createCommand("INSERT INTO target_state (id, on_network) SELECT DISTINCT target_id,1 FROM network_target ON DUPLICATE KEY UPDATE on_network=values(on_network)")->execute();

        // Set the total_points for records
        $this->db->createCommand("UPDATE target_state SET total_points=finding_points+treasure_points")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220514_214530_populate_target_state_with_data cannot be reverted.\n";

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220514_214530_populate_target_state_with_data cannot be reverted.\n";

        return false;
    }
    */
}
