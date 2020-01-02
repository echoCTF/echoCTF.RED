<?php

use yii\db\Migration;

/**
 * Class m200102_123304_update_tai_player_trigger
 */
class m200102_123304_update_tai_player_trigger extends Migration
{
    public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_player}}";
    public $CREATE_SQL="CREATE TRIGGER {{%tai_player}} AFTER INSERT ON {{%player}} FOR EACH ROW
    BEGIN
      DECLARE ltitle VARCHAR(20) DEFAULT 'Joined the platform';
      IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
      END IF;
      SELECT memc_set(CONCAT('player_type:',NEW.id), NEW.type) INTO @devnull;
      SELECT memc_set(CONCAT('player:',NEW.id), NEW.id) INTO @devnull;
      IF NEW.active=1 THEN
        INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.id,'user',NEW.id,0,ltitle,ltitle,ltitle,ltitle,now());
      END IF;
      INSERT INTO profile (player_id) VALUES (NEW.id);
      INSERT INTO player_last (id,on_pui) VALUES (NEW.id,now());
      INSERT INTO player_spin (player_id,counter,total,updated_at) values (NEW.id,0,0,NOW());
    END";

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($this->CREATE_SQL)->execute();

    }

    public function down()
    {
      echo "m200102_123304_update_tai_player_trigger cannot be reverted.\n";
    }
}
