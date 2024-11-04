<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%player}}`.
 */
class m241104_201004_drop_token_columns_from_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("INSERT INTO player_token (player_id,type,token,expires_at,created_at) SELECT id,'password_reset',substr(password_reset_token,1,30),now()+ INTERVAL 24 HOUR,now() FROM player WHERE password_reset_token is not null and password_reset_token!=''")->execute();
      $this->db->createCommand("INSERT INTO player_token (player_id,type,token,expires_at,created_at) SELECT id,'email_verification',substr(verification_token,1,30),now()+ INTERVAL 24 HOUR,now() FROM player WHERE verification_token is not null and verification_token!=''")->execute();
      $this->dropColumn('player', 'password_reset_token');
      $this->dropColumn('player', 'verification_token');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->addColumn('player', 'password_reset_token', $this->string());
      $this->addColumn('player', 'verification_token', $this->string());
    }
}
