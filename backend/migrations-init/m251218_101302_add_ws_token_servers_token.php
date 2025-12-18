<?php

use yii\db\Migration;

class m251218_101302_add_ws_token_servers_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->upsert('ws_token',[
        'token'=>'server123token',
        'subject_id'=>'server-publisher',
        'is_server'=>1,
        'expires_at'=>new \yii\db\Expression('NOW() + INTERVAL 365 DAY')
      ],true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('ws_token',['token'=>'server123token']);
    }
}
