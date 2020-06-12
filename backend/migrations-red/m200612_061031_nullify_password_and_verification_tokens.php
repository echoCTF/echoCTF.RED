<?php

use yii\db\Migration;

/**
 * Class m200612_061031_nullify_password_and_verification_tokens
 */
class m200612_061031_nullify_password_and_verification_tokens extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("UPDATE {{%player}} SET {{%password_reset_token}}=NULL, {{%verification_token}}=NULL WHERE {{%status}}=10")->execute();
      $this->db->createCommand("UPDATE {{%player}} SET {{%password_reset_token}}=NULL WHERE {{%status}}!=10")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200612_061031_nullify_password_and_verification_tokens cannot be reverted.\n";
    }

}
