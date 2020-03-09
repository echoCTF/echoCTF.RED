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
      $CREATE_SQL="INSERT INTO avatar value ('256_10.png');
                   INSERT INTO avatar value ('256_11.png');
                   INSERT INTO avatar value ('256_12.png');
                   INSERT INTO avatar value ('256_13.png');
                   INSERT INTO avatar value ('256_14.png');
                   INSERT INTO avatar value ('256_15.png');
                   INSERT INTO avatar value ('256_16.png');
                   INSERT INTO avatar value ('256_1.png');
                   INSERT INTO avatar value ('256_2.png');
                   INSERT INTO avatar value ('256_3.png');
                   INSERT INTO avatar value ('256_4.png');
                   INSERT INTO avatar value ('256_5.png');
                   INSERT INTO avatar value ('256_6.png');
                   INSERT INTO avatar value ('256_7.png');
                   INSERT INTO avatar value ('256_8.png');
                   INSERT INTO avatar value ('256_9.png');
                   INSERT INTO avatar value ('avatar-1.png');
                   INSERT INTO avatar value ('avatar-2.png');
                   INSERT INTO avatar value ('avatar-3.png');
                   INSERT INTO avatar value ('avatar-4.png');
                   INSERT INTO avatar value ('avatar-5.png');
                   INSERT INTO avatar value ('avatar-6.png');
                   INSERT INTO avatar value ('avatar-7.png');
                   INSERT INTO avatar value ('avatar-8.png');
                   INSERT INTO avatar value ('Bride.png');
                   INSERT INTO avatar value ('default.png');
                   INSERT INTO avatar value ('Franky.png');
                   INSERT INTO avatar value ('Skeleton.png');
                   INSERT INTO avatar value ('users-10.svg');
                   INSERT INTO avatar value ('users-11.svg');
                   INSERT INTO avatar value ('users-12.svg');
                   INSERT INTO avatar value ('users-13.svg');
                   INSERT INTO avatar value ('users-14.svg');
                   INSERT INTO avatar value ('users-15.svg');
                   INSERT INTO avatar value ('users-16.svg');
                   INSERT INTO avatar value ('users-1.svg');
                   INSERT INTO avatar value ('users-2.svg');
                   INSERT INTO avatar value ('users-3.svg');
                   INSERT INTO avatar value ('users-4.svg');
                   INSERT INTO avatar value ('users-5.svg');
                   INSERT INTO avatar value ('users-6.svg');
                   INSERT INTO avatar value ('users-7.svg');
                   INSERT INTO avatar value ('users-8.svg');
                   INSERT INTO avatar value ('users-9.svg');
                   INSERT INTO avatar value ('Vampire-Girl.png');
                   INSERT INTO avatar value ('Vampire.png');
                   INSERT INTO avatar value ('Witch.png');
";
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
