<?php

use yii\db\Migration;

/**
 * Class m240320_235211_alter_openvpn_table_unique_keys
 */
class m240320_235211_alter_openvpn_table_unique_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('name', 'openvpn');
        $this->dropIndex('net', 'openvpn');
        $this->createIndex('server_name_net', 'openvpn', ['server','name','net'], true );


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('server_name_net', 'openvpn');
        $this->createIndex('name', 'openvpn', ['name'], true );
        $this->createIndex('net', 'openvpn', ['name'], true );
    }
}
