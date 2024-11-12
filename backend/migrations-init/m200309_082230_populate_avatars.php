<?php

use yii\db\Migration;

/**
 * Class m200309_082230_populate_avatars
 */
class m200309_082230_populate_avatars extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $CREATE_SQL="INSERT IGNORE INTO avatar value ('256_10.png'),('256_11.png'),('256_12.png'),('256_13.png'),('256_14.png'),
('256_15.png'),('256_16.png'), ('256_1.png'), ('256_2.png'), ('256_3.png'), ('256_4.png'), ('256_5.png'), ('256_6.png'),
('256_7.png'), ('256_8.png'), ('256_9.png'), ('avatar-1.png'), ('avatar-2.png'), ('avatar-3.png'), ('avatar-4.png'), ('avatar-5.png'),
('avatar-6.png'), ('avatar-7.png'), ('avatar-8.png'), ('Bride.png'), ('default.png'), ('Franky.png'),
('Skeleton.png'), ('users-10.svg'), ('users-11.svg'), ('users-12.svg'), ('users-13.svg'), ('users-14.svg'), ('users-15.svg'),
('users-16.svg'), ('users-1.svg'), ('users-2.svg'), ('users-3.svg'), ('users-4.svg'), ('users-5.svg'), ('users-6.svg'),
('users-7.svg'), ('users-8.svg'), ('users-9.svg'), ('Vampire-Girl.png'), ('Vampire.png'), ('Witch.png')";
            $this->db->createCommand($CREATE_SQL)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->db->createCommand("TRUNCATE avatar")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200309_082230_populate_avatars cannot be reverted.\n";

        return false;
    }
    */
}
