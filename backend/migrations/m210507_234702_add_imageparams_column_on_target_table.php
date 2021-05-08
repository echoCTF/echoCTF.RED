<?php

use yii\db\Migration;

/**
 * Class m210507_234702_add_imageparams_column_on_target_table
 */
class m210507_234702_add_imageparams_column_on_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('target', 'imageparams', $this->text());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('target', 'imageparams');
    }

}
