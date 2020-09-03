<?php

use yii\db\Migration;

/**
 * Class m200903_221204_create_give_all_challenge_solvers_their_badge
 */
class m200903_221204_create_give_all_challenge_solvers_their_badge extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("CALL give_all_challenge_solver()")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200903_221204_create_give_all_challenge_solvers_their_badge cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200903_221204_create_give_all_challenge_solvers_their_badge cannot be reverted.\n";

        return false;
    }
    */
}
