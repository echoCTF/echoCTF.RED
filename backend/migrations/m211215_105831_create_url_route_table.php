<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%url_route}}`.
 */
class m211215_105831_create_url_route_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%url_route}}', [
            'id' => $this->primaryKey(),
            'source' => $this->string()->unique()->notNull(),
            'destination' => $this->string()->notNull(),
            'weight'=>$this->integer()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%url_route}}');
    }
}
