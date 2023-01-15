<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%price}}`.
 */
class m230113_220145_create_price_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%price}}', [
            'id' => $this->string(32)->notNull(),
            'active' => $this->boolean(),
            'currency' => $this->string(4)->notNull()->defaultValue('eur'),
            'metadata' => $this->json(),
            'nickname' => $this->string(),
            'product_id' => $this->string(40)->notNull(),
            'recurring_interval' => $this->string()->notNull()->defaultValue("month"),
            'interval_count' => $this->integer()->notNull()->defaultValue(1),
            'ptype' => $this->string(20)->notNull()->defaultValue('recurring'),
            'unit_amount' => 'INTEGER UNSIGNED NOT NULL DEFAULT 0',
            'PRIMARY KEY(id)',
            'CHECK (JSON_VALID(metadata))'
        ]);

        $this->createIndex(
            'idx-price-product_id',
            'price',
            'product_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%price}}');
    }
}
