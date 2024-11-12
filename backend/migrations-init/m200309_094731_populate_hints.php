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
      $this->upsert("hint",['id'=>-1,'title'=>'Welcome to the gig']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
