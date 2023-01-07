<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%notification}}`.
 */
class m230107_123003_add_category_column_to_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%notification}}', 'category', $this->string(20)->notNull()->defaultValue('success')->after('player_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%notification}}', 'category');
    }
}
