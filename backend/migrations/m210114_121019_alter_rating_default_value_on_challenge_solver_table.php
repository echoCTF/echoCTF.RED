<?php

use yii\db\Migration;

/**
 * Class m210114_121019_alter_rating_default_value_on_challenge_solver_table
 */
class m210114_121019_alter_rating_default_value_on_challenge_solver_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->alterColumn('challenge_solver', 'rating', $this->smallInteger()->notNull()->defaultValue(-1));
      $this->update('challenge_solver',['rating'=>-1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->alterColumn('challenge_solver', 'rating', $this->integer());
      $this->update('challenge_solver',['rating'=>null]);

    }
}
