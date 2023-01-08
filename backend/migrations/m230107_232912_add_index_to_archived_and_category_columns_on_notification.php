<?php

use yii\db\Migration;

/**
 * Class m230107_232912_add_index_to_archived_and_category_columns_on_notification
 */
class m230107_232912_add_index_to_archived_and_category_columns_on_notification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-notification-archived',
            'notification',
            'archived'
        );
        $this->createIndex(
            'idx-notification-category',
            'notification',
            'category'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-notification-archived',
            'notification'
        );
        $this->dropIndex(
            'idx-notification-category',
            'notification'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230107_232912_add_index_to_archived_and_category_columns_on_notification cannot be reverted.\n";

        return false;
    }
    */
}
