<?php

use yii\db\Migration;

/**
 * Class m210505_012544_alter_category_column_on_target_table
 */
class m210505_012544_alter_category_column_on_target_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->alterColumn('target', 'parameters', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->alterColumn('target', 'parameters', $this->string());
    }
}
