<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%team}}`.
 */
class m220104_233602_add_inviteonly_column_recruitment_column_to_team_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%team}}', 'inviteonly', $this->boolean()->notNull()->defaultValue(true));
        $this->addColumn('{{%team}}', 'recruitment', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%team}}', 'inviteonly');
        $this->dropColumn('{{%team}}', 'recruitment');
    }
}
