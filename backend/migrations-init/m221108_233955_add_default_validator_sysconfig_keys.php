<?php

use yii\db\Migration;

/**
 * Class m221108_233955_add_default_validator_sysconfig_keys
 */
class m221108_233955_add_default_validator_sysconfig_keys extends Migration
{
    public $keys=[
        'verification_resend_ip' => 0,
        'verification_resend_ip_timeout'=>60*60,
        'verification_resend_email' => 3,
        'verification_resend_email_timeout'=>60*60,
        'password_reset_ip' => 0,
        'password_reset_ip_timeout'=>60*60,
        'password_reset_email' => 3,
        'password_reset_email_timeout'=>60*60,
        'signup_TotalRegistrationsValidator' =>0,
        'signup_HourRegistrationValidator' =>0,
        'signup_StopForumSpamValidator' =>0,
        'signup_MXServersValidator' =>0,
        'failed_login_ip' => 0,
        'failed_login_ip_timeout'=>15*60,
        'failed_login_username' => 10,
        'failed_login_username_timeout'=>15*60,
        'username_length_min' => 4,
        'username_length_max' => 32,
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach($this->keys as $key => $val)
            $this->upsert('sysconfig',['id'=>$key,'val'=>$val],true);
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221108_233955_add_default_validator_sysconfig_keys cannot be reverted.\n";
    }
}
