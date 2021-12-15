<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%stripe_webhook}}`.
 */
class m210203_214148_create_stripe_webhook_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%stripe_webhook}}', [
            'id' => $this->primaryKey(),
            'type'=>$this->string()->notNull(),
            'object'=>$this->text(),
            'object_id'=>$this->string(),
            'ts'=>$this->timestamp()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%stripe_webhook}}');
    }
}
