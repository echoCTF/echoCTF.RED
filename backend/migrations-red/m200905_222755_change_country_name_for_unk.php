<?php

use yii\db\Migration;

/**
 * Class m200905_222755_change_country_name_for_unk
 */
class m200905_222755_change_country_name_for_unk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->update("country",['name'=>'Unknown'], ['id'=>'unk']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->update("country",['name'=>'Not set'], ['id'=>'unk']);
    }
}
