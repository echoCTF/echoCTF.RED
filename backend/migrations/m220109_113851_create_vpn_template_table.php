<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vpn_template}}`.
 */
class m220109_113851_create_vpn_template_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%vpn_template}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'client' => $this->boolean()->notNull()->defaultValue(1),
            'server' => $this->boolean()->notNull()->defaultValue(0),
            'active' => $this->boolean()->notNull()->defaultValue(1),
            'visible' => $this->boolean()->notNull()->defaultValue(1),
            'filename' => $this->string()->notNull()->defaultValue('echoCTF.ovpn'),
            'description' => $this->text(),
            'content' => $this->text(),
            'created_at' => $this->datetime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%vpn_template}}');
    }
}
