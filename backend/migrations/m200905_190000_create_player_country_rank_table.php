<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_country_rank}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%player}}`
 */
class m200905_190000_create_player_country_rank_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%player_country_rank}}', [
            'id' => $this->integer()->notNull()->defaultValue(0),
            'player_id' => $this->integer()->unsigned()->notNull(),
            'country' => $this->string(3)->notNull(),
        ],'ENGINE=MEMORY');
        $this->addPrimaryKey('{{%PK-player_country_rank}}', '{{%player_country_rank}}', ['id', 'country']);

        // creates index for column `player_id`
        $this->createIndex(
            '{{%idx-player_country_rank-player_id}}',
            '{{%player_country_rank}}',
            'player_id',
            true
        );

        // add foreign key for table `{{%player}}`
        $this->addForeignKey(
            '{{%fk-player_country_rank-player_id}}',
            '{{%player_country_rank}}',
            'player_id',
            '{{%player}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // drops foreign key for table `{{%player}}`
        $this->dropForeignKey(
            '{{%fk-player_country_rank-player_id}}',
            '{{%player_country_rank}}'
        );

        // drops index for column `player_id`
        $this->dropIndex(
            '{{%idx-player_country_rank-player_id}}',
            '{{%player_country_rank}}'
        );

        $this->dropTable('{{%player_country_rank}}');
    }
}
