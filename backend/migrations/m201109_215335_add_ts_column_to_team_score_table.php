<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%team_score}}`.
 */
class m201109_215335_add_ts_column_to_team_score_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%team_score}}', 'ts', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%team_score}}', 'ts');
    }
}
