<?php

use yii\db\Migration;

/**
 * Class m200309_094731_populate_hints
 */
class m200309_094731_populate_hints extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("insert into hint (id,title) values (-1,'Welcome to the gig')")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
