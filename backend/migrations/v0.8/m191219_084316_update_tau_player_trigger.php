<?php

use yii\db\Migration;

/**
 * Class m191219_084316_update_tau_player_trigger
 */
class m191219_084316_update_tau_player_trigger extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tau_player}}";
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    $CREATE_SQL="CREATE TRIGGER {{%tau_player}} AFTER UPDATE ON {{%player}} FOR EACH ROW
BEGIN
IF (select memc_server_count()<1) THEN
  select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
END IF;
IF NEW.type!=OLD.type THEN
  SELECT memc_set(CONCAT('player_type:',NEW.id), NEW.type) INTO @devnull;
END IF;

IF (NEW.active!=OLD.active AND NEW.active=1) THEN
  SELECT memc_get('sysconfig:teams') INTO @teams;
  SELECT memc_get('sysconfig:require_activation') INTO @require_activation;
-- Create the user rank
  REPLACE INTO player_rank(id,player_id) SELECT max(id)+1,NEW.id FROM player_rank;
  IF @teams IS NOT NULL AND @teams=true AND @require_activation IS NOT NULL and @require_activation=1 THEN
    UPDATE team_player SET approved=1 WHERE player_id=NEW.id;
  END IF;

  SET @ltitle=concat('Joined the platform');
  INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.id,'user',NEW.id,0,@ltitle,@ltitle,@ltitle,@ltitle,now());
END IF;
END
";
      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($CREATE_SQL)->execute();

    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      return true;
    }
}
