<?php

use yii\db\Migration;

/**
 * Class m220425_221753_alter_table_player_counter_nf_primary_key
 */
class m220425_221753_alter_table_player_counter_nf_primary_key extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->dropPrimaryKey('PRIMARY','player_counter_nf');
        $this->alterColumn('player_counter_nf', 'player_id', 'INT NOT NULL');
        $this->dropIndex('player_id','player_counter_nf');
        $this->addPrimaryKey('player_counter_nf-player_id-metric', 'player_counter_nf', ['player_id', 'metric']);
        $this->update('player_counter_nf',['counter'=>0]);
    }

    public function down()
    {
        $this->dropPrimaryKey('PRIMARY','player_counter_nf');
        $this->alterColumn('player_counter_nf', 'player_id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
        $this->createIndex(
            '{{player_id}}',
            '{{%player_counter_nf}}',
            ['player_id', 'metric'],
            true
        );
    }
}
