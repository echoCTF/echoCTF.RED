<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%openvpn}}`.
 */
class m220513_002645_create_openvpn_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%openvpn}}', [
            'id' => $this->primaryKey(),
            'provider_id'=>$this->string(), // eg vultr-instance-id to determine the server that will use this
            'name' => $this->string()->unique(), // openvpn_tun0
            'net' => $this->integer()->unsigned()->unique(), // 10.10.0.0
            'mask' => $this->integer()->unsigned(), // 255.255.0.0
            'management_ip' => $this->integer()->unsigned(), // 127.0.0.1
            'management_port' => $this->smallInteger()->unsigned(), // 11195
            'management_passwd' => $this->string(), // mypass
            'status_log'=>$this->string(), // eg /var/log/openvpn-status.log
            'conf' => $this->text(),
            'updated_at' => $this->timestamp(),
            'created_at' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%openvpn}}');
    }
}
