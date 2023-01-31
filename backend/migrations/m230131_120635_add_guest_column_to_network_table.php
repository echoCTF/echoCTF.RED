<?php

use yii\db\Migration;

/**
 * Class m230131_120635_add_guest_column_to_network_table
 */
class m230131_120635_add_guest_column_to_network_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%network}}', 'guest', $this->boolean()->notNull()->defaultValue(0)->after('public'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%network}}', 'guest');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230131_120635_add_guest_columnt_to_network_table cannot be reverted.\n";

        return false;
    }
    */
}
