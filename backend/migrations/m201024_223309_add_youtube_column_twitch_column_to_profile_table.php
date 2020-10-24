<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%profile}}`.
 */
class m201024_223309_add_youtube_column_twitch_column_to_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%profile}}', 'youtube', $this->string());
        $this->addColumn('{{%profile}}', 'twitch', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%profile}}', 'youtube');
        $this->dropColumn('{{%profile}}', 'twitch');
    }
}
