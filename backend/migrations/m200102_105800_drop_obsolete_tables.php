<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%sshkey}}`.
 * Handles the dropping of table `{{%bridge_ruleset}}`.
 * Handles the dropping of table `{{%finding_packet}}`.
 * Handles the dropping of table `{{%player_ip}}`.
 * Handles the dropping of table `{{%player_mac}}`.
 * Handles the dropping of table `{{%arpdat}}`.
 * Handles the dropping of table `{{%player_mac_mem}}`.
 * Handles the dropping of table `{{%player_mem}}`.
 * Handles the dropping of table `{{%target_mem}}`.
 * Handles the dropping of table `{{%tcpdump}}`.
 * Handles the dropping of table `{{%vtcpdump}}`.
 */
class m200102_105800_drop_obsolete_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->dropTable('{{%sshkey}}');
        $this->dropTable('{{%bridge_ruleset}}');
        $this->dropTable('{{%finding_packet}}');
        $this->dropTable('{{%player_ip}}');
        $this->dropTable('{{%vtcpdump}}');
        $this->dropTable('{{%tcpdump}}');
        $this->dropTable('{{%target_mem}}');
        $this->dropTable('{{%player_mac_mem}}');
        $this->dropTable('{{%arpdat}}');
        $this->dropTable('{{%player_mac}}');
        $this->dropTable('{{%player_mem}}');
        $this->dropTable('{{%tcpdump_bh}}');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
      return true;
    }
}
