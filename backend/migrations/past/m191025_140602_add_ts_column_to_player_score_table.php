<?php

use yii\db\Migration;

/**
 * Handles adding ts to table `{{%player_score}}`.
 */
class m191025_140602_add_ts_column_to_player_score_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%player_score}}', '{{%ts}}', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('{{%player_score}}', '{{%ts}}');
    }
}
