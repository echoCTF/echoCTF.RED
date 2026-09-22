<?php

use yii\db\Migration;

class m000000_000005_update_target_weights extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            UPDATE target t
            JOIN (
                SELECT id, (ROW_NUMBER() OVER (ORDER BY difficulty, name, id) - 1) * 10 AS w
                FROM target
                WHERE id > 1
            ) r ON t.id = r.id
            SET t.weight = r.w
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m000000_000005_update_target_weights cannot be reverted.\n";
    }

}
