<?php

use yii\db\Migration;

/**
 * Handles adding auth_key, password_hash, password_reset_token, status, verification_token, admin to table `{{%player}}`.
 */
class m191118_110853_add_auth_key_column_password_hash_column_password_reset_token_column_verification_token_column_admin_column_to_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%player}}', 'auth_key', $this->string(32)->notNull());
        $this->addColumn('{{%player}}', 'password_hash', $this->string()->notNull());
        $this->addColumn('{{%player}}', 'password_reset_token', $this->string()->defaultValue(NULL));
        $this->addColumn('{{%player}}', 'verification_token', $this->string()->defaultValue(NULL));
        $this->addColumn('{{%player}}', 'admin', $this->smallInteger()->defaultValue(0));
        $this->db->createCommand("UPDATE {{%player}} SET {{%password_hash}}={{%password}}")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%player}}', 'auth_key');
        $this->dropColumn('{{%player}}', 'password_hash');
        $this->dropColumn('{{%player}}', 'password_reset_token');
        $this->dropColumn('{{%player}}', 'verification_token');
        $this->dropColumn('{{%player}}', 'admin');
    }
}
