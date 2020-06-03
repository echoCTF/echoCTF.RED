<?php

use yii\db\Migration;

/**
 * Class m200603_144142_cleanup_old_notifications
 */
class m200603_144142_cleanup_old_notifications extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("DELETE FROM {{%notification}} WHERE {{%updated_at}} IS NULL AND {{%archived}}=1")->execute();
      $this->db->createCommand("DELETE FROM {{%notification}} WHERE {{%updated_at}} IS NOT NULL AND {{%archived}}=1 AND {{%updated_at}} < NOW() - INTERVAL 7 DAY")->execute();
      $this->db->createCommand("DELETE FROM {{%notification}} WHERE {{%title}}='echoCTF RED v0.7 is here' OR {{%title}}='echoCTF RED v0.6 is here' OR {{%title}}='Restarts counter zeroed'")->execute();
      $this->db->createCommand("DELETE FROM {{%notification}} WHERE {{%title}} LIKE '%22/10//2019%' OR {{%title}} LIKE '%23/12/2019%'")->execute();
      $this->db->createCommand("DELETE FROM {{%notification}} WHERE {{%updated_at}} IS NOT NULL AND {{%archived}}=0 AND {{%title}} LIKE '%restart request completed' AND {{%updated_at}}<NOW() - INTERVAL 10 DAY")->execute();
      $this->db->createCommand("DELETE FROM {{%notification}} WHERE {{%title}} LIKE 'Scheduled maintenance%'")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200603_144142_cleanup_old_notifications cannot be reverted.\n";
    }
}
