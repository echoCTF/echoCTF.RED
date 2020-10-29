<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%challenge}}`.
 */
class m201029_093046_add_icon_column_to_challenge_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%challenge}}', 'icon', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%challenge}}', 'icon');
    }
}
