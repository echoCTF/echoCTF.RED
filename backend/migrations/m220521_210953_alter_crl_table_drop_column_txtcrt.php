<?php

use yii\db\Migration;

/**
 * Class m220521_210953_alter_crl_table_drop_column_txtcrt
 */
class m220521_210953_alter_crl_table_drop_column_txtcrt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('crl', 'txtcrt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('crl', 'txtcrt', 'MEDIUMTEXT NOT NULL');
    }
}
