<?php

use yii\db\Migration;

/**
 * Class m201028_214951_remove_existing_network_records
 */
class m201028_214951_remove_existing_network_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("TRUNCATE network_player")->execute();
      $this->db->createCommand("TRUNCATE network_target")->execute();
      $this->db->createCommand("DELETE FROM network")->execute();
      $this->db->createCommand("ALTER TABLE network AUTO_INCREMENT=0")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

}
