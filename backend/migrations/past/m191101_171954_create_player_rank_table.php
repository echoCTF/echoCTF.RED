<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_rank}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m191101_171954_create_player_rank_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%player_rank}}', [
            'id' => $this->integer()->unsigned(),
            'player_id' => $this->integer()->notNull()->unique(),
            'PRIMARY KEY (id)',
        ],'ENGINE=MEMORY');

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-player_rank-player_id}}',
            '{{%player_rank}}',
            'player_id'
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-player_rank-player_id}}',
            '{{%player_rank}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );
        $QUERY="INSERT INTO player_rank (player_id,id) select t.player_id,@curRank:=@curRank+1 AS rank from player_score as t,(select @curRank:=0) r order by points desc,t.player_id";
        $this->db->createCommand($QUERY)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-player_rank-player_id}}',
            '{{%player_rank}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-player_rank-player_id}}',
            '{{%player_rank}}'
        );

        $this->dropTable('{{%player_rank}}');
    }
}
