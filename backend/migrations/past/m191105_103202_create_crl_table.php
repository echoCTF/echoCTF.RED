<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%crl}}`.
 */
class m191105_103202_create_crl_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%crl}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer(),
            'subject' => $this->text(),
            'csr' => $this->text(),
            'crt' => $this->text(),
            'txtcrt' => $this->text(),
            'privkey' => $this->text(),
            'ts' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%crl}}');
    }
}
