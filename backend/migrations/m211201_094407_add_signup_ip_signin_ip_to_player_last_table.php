<?php

use yii\db\Migration;

/**
 * Class m211201_094407_add_registration_ip_login_ip_to_player_last_table
 */
class m211201_094407_add_signup_ip_signin_ip_to_player_last_table extends Migration
{

    /**
     * {@inheritdoc}
     */
     public function safeUp()
     {
       $this->addColumn('player_last', 'signup_ip', 'INT UNSIGNED AFTER vpn_local_address');
       $this->addColumn('player_last', 'signin_ip', 'INT UNSIGNED AFTER signup_ip');
       $this->createIndex(
             'idx-player_last-signup_ip',
             'player_last',
             'signup_ip'
         );
       $this->createIndex(
             'idx-player_last-signin_ip',
             'player_last',
             'signin_ip'
         );
     }

     /**
      * {@inheritdoc}
      */
     public function safeDown()
     {
       $this->dropIndex(
              'idx-player_last-signup_ip',
              'player_last'
          );
       $this->dropIndex(
              'idx-player_last-signin_ip',
              'player_last'
          );
       $this->dropColumn('player_last', 'signup_ip');
       $this->dropColumn('player_last', 'signin_ip');
     }

}
