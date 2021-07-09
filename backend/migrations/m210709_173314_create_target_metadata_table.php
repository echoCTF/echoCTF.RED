<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%target_metadata}}`.
 */
class m210709_173314_create_target_metadata_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%target_metadata}}', [
            'target_id' => $this->primaryKey(),
            'scenario' => $this->text(),
            'instructions' => $this->text(),
            'solution' => $this->text(),
            'pre_credits' => $this->text(),
            'post_credits' => $this->text(),
            'pre_exploitation'=> $this->text(),
            'post_exploitation'=> $this->text(),
            'created_at'=> $this->datetime(),
            'updated_at'=> $this->dateTime(),

        ]);
        $this->addForeignKey(
          '{{%fk-target_metadata-target_id}}',
          '{{%target_metadata}}',
          'target_id',
          '{{%target}}',
          'id',
          'CASCADE'
      );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%target_metadata}}');
    }
}
