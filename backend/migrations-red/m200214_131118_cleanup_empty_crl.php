<?php

use yii\db\Migration;

/**
 * Class m200214_131118_cleanup_empty_crl
 */
class m200214_131118_cleanup_empty_crl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("DELETE FROM crl WHERE subject=''")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
