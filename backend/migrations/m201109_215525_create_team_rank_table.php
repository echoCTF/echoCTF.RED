<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%team_rank}}`.
 */
class m201109_215525_create_team_rank_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%team_rank}}', [
            'id' => $this->integer()->notNull()->defaultValue(0),
            'team_id' => $this->integer()->notNull()->unique(),
        ],'ENGINE MEMORY');
        $this->addPrimaryKey('team_rank-pk', '{{%team_rank}}', ['id', 'team_id']);
        $this->createIndex(
             'idx-team_rank-team_id',
             '{{%team_rank}}',
             'team_id'
         );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%team_rank}}');
    }
}
