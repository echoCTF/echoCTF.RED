<?php

use yii\db\Migration;

/**
 * Class m231101_205544_add_unique_to_title_faq
 */
class m231101_205544_add_unique_to_title_faq extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'unique_faq_title',
            '{{%faq}}',
            'title',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('unique_faq_title','faq');
    }

}
