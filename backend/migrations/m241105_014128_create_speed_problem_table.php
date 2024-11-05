<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%speed_problem}}`.
 */
class m241105_014128_create_speed_problem_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createTable('{{%speed_problem}}', [
        'id' => $this->primaryKey(),
        'name' => $this->string(),
        'description' => $this->text(),
        'server' => $this->string(),
        'challenge_image' => $this->string(),
        'validator_image' => $this->string(),
        'created_at' => $this->datetime(),
        'updated_at' => $this->datetime(),
      ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%speed_problem}}');
    }
}
