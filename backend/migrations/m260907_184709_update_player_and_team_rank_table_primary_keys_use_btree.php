<?php

use yii\db\Migration;

class m260907_184709_update_player_and_team_rank_table_primary_keys_use_btree extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("ALTER TABLE player_rank DROP PRIMARY KEY, ADD PRIMARY KEY (id,player_id) USING BTREE")->execute();
      $this->db->createCommand("ALTER TABLE team_rank DROP PRIMARY KEY, ADD PRIMARY KEY (id,team_id) USING BTREE")->execute();
      $this->db->createCommand("ALTER TABLE player_country_rank DROP PRIMARY KEY, ADD PRIMARY KEY (`id`,`country`) USING BTREE")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->db->createCommand("ALTER TABLE player_rank DROP PRIMARY KEY, ADD PRIMARY KEY (id,player_id)")->execute();
      $this->db->createCommand("ALTER TABLE team_rank DROP PRIMARY KEY, ADD PRIMARY KEY (id,team_id)")->execute();
      $this->db->createCommand("ALTER TABLE player_country_rank DROP PRIMARY KEY, ADD PRIMARY KEY (`id`,`country`)")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260907_184709_update_player_and_team_rank_table_primary_keys_use_btree cannot be reverted.\n";

        return false;
    }
    */
}
