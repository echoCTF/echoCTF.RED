<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tutorial_task_dependency}}`.
 */
class m200107_082231_create_tutorial_task_dependency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tutorial_task_dependency}}', [
            'id' => $this->primaryKey(),
            'tutorial_task_id' => $this->integer(),
            'item_id' => $this->integer()->notNull(),
            'item' => $this->string(),
        ]);

        // creates index for column `tutorial_task_id`
        $this->createIndex(
            '{{%idx-tutorial_task_dependency-tutorial_task_id}}',
            '{{%tutorial_task_dependency}}',
            'tutorial_task_id'
        );

        // add foreign key for table `{{%tutorial}}`
        $this->addForeignKey(
            '{{%fk-tutorial_task_dependency-tutorial_task_id}}',
            '{{%tutorial_task_dependency}}',
            'tutorial_task_id',
            '{{%tutorial_task}}',
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
          '{{%fk-tutorial_task_dependency-tutorial_task_id}}',
          '{{%tutorial_task_dependency}}'
      );

      // drops index for column `tutorial_id`
      $this->dropIndex(
          '{{%idx-tutorial_task_dependency-tutorial_task_id}}',
          '{{%tutorial_task_dependency}}'
      );
        $this->dropTable('{{%tutorial_task_dependency}}');
    }
}
