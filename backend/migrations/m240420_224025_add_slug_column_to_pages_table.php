<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%page}}`.
 */
class m240420_224025_add_slug_column_to_pages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%pages}}', 'slug', $this->string()->after('title'));
      $this->execute('UPDATE pages SET slug=title');
      $this->alterColumn('{{%pages}}','slug',$this->string()->after('title')->notNull()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%page}}', 'slug');
    }
}
