<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%treasure}}`.
 */
class m200518_111954_add_location_suggestion_solution_columns_to_treasure_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%treasure}}', 'location', $this->string(255));
        $this->addColumn('{{%treasure}}', 'suggestion', $this->text());
        $this->addColumn('{{%treasure}}', 'solution', 'LONGTEXT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%treasure}}', 'location');
        $this->dropColumn('{{%treasure}}', 'suggestion');
        $this->dropColumn('{{%treasure}}', 'solution');
    }
}
