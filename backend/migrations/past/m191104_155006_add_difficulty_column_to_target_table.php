<?php

use yii\db\Migration;

/**
 * Handles adding difficulty to table `{{%target}}`.
 */
class m191104_155006_add_difficulty_column_to_target_table extends Migration
{
  public $update_targets=[
    ['id'=> 6, 'difficulty'=> 2],
    ['id'=> 7, 'difficulty'=> 1],
    ['id'=> 10, 'difficulty'=> 3],
    ['id'=> 4, 'difficulty'=> 3],
    ['id'=> 5, 'difficulty'=> 0],
    ['id'=> 2, 'difficulty'=> 2],
    ['id'=> 3, 'difficulty'=> 2],
    ['id'=> 9, 'difficulty'=> 4],
    ['id'=> 8, 'difficulty'=> 1],
];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%target}}', 'difficulty', $this->integer()->defaultValue(0));
        foreach($this->update_targets as $rec) $this->db->createCommand("UPDATE {{%target}} SET {{%difficulty}}=:difficulty WHERE id=:id")->bindParam(':difficulty',$rec['difficulty'])->bindParam(':id',$rec['id'])->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%target}}', 'difficulty');
    }
}
