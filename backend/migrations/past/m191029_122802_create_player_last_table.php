<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_last}}`.
 */
class m191029_122802_create_player_last_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_last}}', [
            'id' => $this->primaryKey(),
            'ts'=>$this->timestamp(), // This is so that we can trick mysql
            'on_pui' => $this->timestamp(),
            'on_vpn' => $this->timestamp(),
            'vpn_remote_address'=>$this->integer(),
            'vpn_local_address'=>$this->integer(),
        ]);

        $this->db->createCommand("INSERT INTO {{%player_last}} ({{%id}},{{%on_pui}}) SELECT {{%id}},from_unixtime(memc_get(concat('last_seen:',id))) as last_seen FROM {{%player}} WHERE {{%active}}=1 HAVING last_seen IS NOT NULL ON DUPLICATE KEY UPDATE {{%on_pui}}=values({{%on_pui}});
INSERT INTO {{%player_last}} ({{%id}},{{%vpn_local_address}},{{%vpn_remote_address}}) SELECT {{%id}},INET_ATON(memc_get(concat('ovpn:',id))) as vpn_local_address,INET_ATON(memc_get(concat('ovpn_remote:',id))) as vpn_remote_address FROM {{%player}} WHERE {{%active}}=1 ON DUPLICATE KEY UPDATE {{%vpn_local_address}}=values({{%vpn_local_address}}),{{%vpn_remote_address}}=values({{%vpn_remote_address}});
INSERT INTO {{%player_last}} ({{%id}},{{%on_vpn}}) SELECT {{%id}},now() FROM {{%player}} WHERE {{%active}}=1 HAVING memc_get(concat('ovpn:',id)) IS NOT NULL ON DUPLICATE KEY UPDATE on_vpn=values(on_vpn);")->execute();

        $this->createIndex(
            '{{%idx-player_last-on_pui}}',
            '{{%player_last}}',
            'on_pui'
        );
        $this->createIndex(
            '{{%idx-player_last-on_vpn}}',
            '{{%player_last}}',
            'on_vpn'
        );
        $this->createIndex(
            '{{%idx-player_last-vpn_remote_address}}',
            '{{%player_last}}',
            'vpn_remote_address'
        );
        $this->createIndex(
            '{{%idx-player_last-vpn_local_address}}',
            '{{%player_last}}',
            'vpn_local_address'
        );
        $this->createIndex(
            '{{%idx-player_last-combined}}',
            '{{%player_last}}',
            ['vpn_local_address','vpn_remote_address','on_vpn', 'on_pui']
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%player_last}}');
    }
}
