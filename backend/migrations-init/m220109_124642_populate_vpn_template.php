<?php

use yii\db\Migration;

/**
 * Class m220109_124642_populate_vpn_template
 */
class m220109_124642_populate_vpn_template extends Migration
{

   public $template=[
     'name'=> 'echoCTF',
     'filename'=> 'echoCTF.ovpn',
     'description' =>'echoCTF.RED OpenVPN client configuration',
     'active' =>1,
     'visible' =>1,
     'client' =>1,
     'server' =>0,
   ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->template['content']=file_get_contents(Yii::getAlias('@app/modules/frontend/views/player/ovpn.php'));
      $this->upsert('{{%vpn_template}}',$this->template);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('{{%vpn_template}}',$this->template);
    }
}
