<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product}}`.
 */
class m210206_125913_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product}}', [
            'id' => $this->string(40)->notNull()->append('PRIMARY KEY'),
            'shortcode' => $this->string(40),
            'active'=>$this->boolean()->notNull()->defaultValue(0),
            'name'=>$this->string()->notNull(),
            'description'=>$this->text(),
            'livemode'=>$this->boolean()->notNull()->defaultValue(0),
            'metadata'=>$this->text(),
            'htmlOptions'=>$this->text(),
            'perks'=>$this->text(),
            'weight'=>$this->integer()->notNull()->defaultValue(0),
            'created_at'=> $this->datetime(),
            'updated_at'=> $this->datetime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product}}');
    }
}
