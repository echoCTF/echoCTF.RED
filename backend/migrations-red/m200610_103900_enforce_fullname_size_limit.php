<?php

use yii\db\Migration;

/**
 * Class m200610_103900_enforce_fullname_size_limit
 */
class m200610_103900_enforce_fullname_size_limit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("UPDATE player SET fullname=SUBSTRING(fullname,1,32) WHERE CHAR_LENGTH(fullname)>32")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200610_103900_enforce_fullname_size_limit cannot be reverted.\n";
    }

}
