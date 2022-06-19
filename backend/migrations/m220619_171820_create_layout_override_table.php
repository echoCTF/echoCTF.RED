<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%layout_override}}`.
 */
class m220619_171820_create_layout_override_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%layout_override}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'route' => $this->string(),
            'guest' => $this->boolean()->notNull()->defaultValue(0),
            'player_id' => $this->integer(),
            'css' => $this->text(),
            'js' => $this->text(),
            'repeating' => $this->boolean()->notNull()->defaultValue(0),
            'valid_from' => $this->dateTime()->notNull(),
            'valid_until' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%layout_override}}');
    }
}
