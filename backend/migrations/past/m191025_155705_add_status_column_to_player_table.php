<?php

use yii\db\Migration;

/**
 * Handles adding status to table `{{%player}}`.
 */
class m191025_155705_add_status_column_to_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%player}}', '{{%status}}',$this->integer()->defaultValue(0)->after('academic'));
      $this->createIndex(
          '{{%idx-player-status}}',
          '{{%player}}',
          'status'
      );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropIndex(
          '{{%idx-player-status}}',
          '{{%player}}'
      );
      $this->dropColumn('{{%player}}', '{{%status}}');
    }
}
