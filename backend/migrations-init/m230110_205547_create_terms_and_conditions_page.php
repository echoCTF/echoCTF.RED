<?php

use yii\db\Migration;

/**
 * Class m230110_205547_create_terms_and_conditions_page
 */
class m230110_205547_create_terms_and_conditions_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pages=intval($this->db->createCommand("SELECT count(*) FROM pages WHERE id=1")->queryScalar());
        if($pages===0)
        {
            $this->insert('pages',['title'=>'Privacy Policy','body'=>'Privacy Policy']);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230110_205547_create_terms_and_conditions_page cannot be reverted.\n";

        return false;
    }
}
