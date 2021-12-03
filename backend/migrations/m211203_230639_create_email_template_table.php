<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%email_template}}`.
 */
class m211203_230639_create_email_template_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%email_template}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32),
            'title' => $this->string(),
            'html' => $this->text(),
            'txt' => $this->text(),
            'created_at'=>$this->datetime(),
            'updated_at'=>$this->datetime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email_template}}');
    }
}
