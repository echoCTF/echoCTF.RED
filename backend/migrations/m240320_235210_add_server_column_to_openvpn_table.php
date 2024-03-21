<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%openvpn}}`.
 */
class m240320_235210_add_server_column_to_openvpn_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%openvpn}}', 'server', $this->string()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%openvpn}}', 'server');
    }
}
