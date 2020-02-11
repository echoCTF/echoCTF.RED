<?php

use yii\db\Migration;

/**
 * Class m200211_094104_ensure_existing_active_profiles_have_accepted_terms_and_gdpr
 */
class m200211_094104_ensure_existing_active_profiles_have_accepted_terms_and_gdpr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("update profile set terms_and_conditions=1, mail_optin=1,gdpr=1")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

}
