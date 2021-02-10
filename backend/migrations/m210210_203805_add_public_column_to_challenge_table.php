<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%challenge}}`.
 */
class m210210_203805_add_public_column_to_challenge_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%challenge}}', 'public', $this->boolean()->notNull()->defaultValue(1));
      $this->createIndex(
          '{{%idx-challenge-public}}',
          '{{%challenge}}',
          'public'
      );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('{{%challenge}}', 'public');
    }
}
