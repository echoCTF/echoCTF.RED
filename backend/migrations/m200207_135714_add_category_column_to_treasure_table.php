<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%treasure}}`.
 */
class m200207_135714_add_category_column_to_treasure_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%treasure}}', 'category', $this->string()->notNull()->defaultValue('other'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%treasure}}', 'category');
    }
}
