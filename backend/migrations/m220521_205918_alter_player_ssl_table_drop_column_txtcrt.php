<?php

use yii\db\Migration;

/**
 * Class m220521_205918_alter_player_ssl_table_drop_column_txtcrt
 */
class m220521_205918_alter_player_ssl_table_drop_column_txtcrt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('player_ssl', 'txtcrt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('player_ssl', 'txtcrt', 'MEDIUMTEXT NOT NULL');
    }
}
