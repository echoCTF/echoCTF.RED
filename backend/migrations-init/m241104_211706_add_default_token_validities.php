<?php

use yii\db\Migration;

/**
 * Class m241104_211706_add_default_token_validities
 */
class m241104_211706_add_default_token_validities extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->upsert('sysconfig',['id'=>'password_reset_token_validity','val'=>'24 hour'],true);
      $this->upsert('sysconfig',['id'=>'mail_verification_token_validity','val'=>'10 day'],true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241104_211706_add_default_token_validities cannot be reverted.\n";
    }
}
