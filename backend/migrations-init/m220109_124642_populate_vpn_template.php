<?php

use yii\db\Migration;

/**
 * Class m220109_124642_populate_vpn_template
 */
class m220109_124642_populate_vpn_template extends Migration
{
   public $initial_template=[
     'name'=> 'echoCTF24',
     'filename'=> 'echoCTF24.ovpn',
     'description' =>'echoCTF.RED OpenVPN v2.4 client configuration',
     //'content' =>file_get_content('/etc/passwd'),
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
      $this->initial_template['content']=file_get_contents(Yii::getAlias('@app/modules/frontend/views/player/ovpn.php'));
      $this->upsert('{{%vpn_template}}',$this->initial_template);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('{{%vpn_template}}',$this->initial_template);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220109_124642_populate_vpn_template cannot be reverted.\n";

        return false;
    }
    */
}
