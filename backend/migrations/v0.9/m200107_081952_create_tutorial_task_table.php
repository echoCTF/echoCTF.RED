<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tutorial_task}}`.
 */
class m200107_081952_create_tutorial_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tutorial_task}}', [
            'id' => $this->primaryKey(),
            'tutorial_id' => $this->integer(),
            'title' => $this->string(),
            'description' => $this->text(),
            'points' => $this->integer()->defaultValue(0),
            'answer' => $this->string(),
            'weight' => $this->smallInteger(),
        ]);
    // creates index for column `tutorial_id`
      $this->createIndex(
          '{{%idx-tutorial_task-tutorial_id}}',
          '{{%tutorial_task}}',
          'tutorial_id'
      );

      // add foreign key for table `{{%tutorial}}`
      $this->addForeignKey(
          '{{%fk-tutorial_task-tutorial_id}}',
          '{{%tutorial_task}}',
          'tutorial_id',
          '{{%tutorial}}',
          'id',
          'CASCADE'
      );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-tutorial_task-tutorial_id}}',
            '{{%tutorial_task}}'
        );

        // drops index for column `tutorial_id`
        $this->dropIndex(
            '{{%idx-tutorial_task-tutorial_id}}',
            '{{%tutorial_task}}'
        );
        $this->dropTable('{{%tutorial_task}}');
    }
}
