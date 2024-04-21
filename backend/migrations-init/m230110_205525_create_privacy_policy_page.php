<?php

use yii\db\Migration;

/**
 * Class m230110_205525_create_privacy_policy_page
 */
class m230110_205525_create_privacy_policy_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pages=intval($this->db->createCommand("SELECT count(*) FROM pages WHERE id=2")->queryScalar());
        if($pages===0)
        {
            $this->insert('pages',['title'=>'Privacy Policy','body'=>'Privacy Policy','slug'=>'privacy-policy']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230110_205525_create_privacy_policy_page cannot be reverted.\n";

    }
}
