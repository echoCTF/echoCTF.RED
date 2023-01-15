<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%product}}`.
 */
class m230114_000209_drop_price_related_columns_from_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('product', 'price_id');
        $this->dropColumn('product', 'currency');
        $this->dropColumn('product', 'unit_amount');
        $this->dropColumn('product', 'interval');
        $this->dropColumn('product', 'interval_count');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('product', 'price_id', $this->string(40)->notNull());
        $this->addColumn('product', 'currency',$this->string(40)->notNull());
        $this->addColumn('product', 'unit_amount',$this->bigInteger()->notNull()->defaultValue(0));
        $this->addColumn('product', 'interval',$this->string(20)->notNull()->defaultValue('day'));
        $this->addColumn('product', 'interval_count',$this->bigInteger()->notNull()->defaultValue(1));
    }
}
