<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%experience}}`.
 */
class m191104_173035_create_experience_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%experience}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'category' => $this->string(32),
            'description' => $this->text(),
            'icon' => $this->string(),
            'min_points' => $this->integer(),
            'max_points' => $this->integer(),
        ]);
        $this->createIndex(
            'idx-experience-points',
            '{{%experience}}',
            ['min_points','max_points']
        );
        $this->createIndex(
            'idx-experience-points-category',
            '{{%experience}}',
            ['min_points','max_points', 'category']
        );
        $this->createIndex(
            'idx-experience-category',
            '{{%experience}}',
            'category'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%experience}}');
    }
}
