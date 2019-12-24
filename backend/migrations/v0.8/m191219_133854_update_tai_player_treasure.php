<?php

use yii\db\Migration;

/**
 * Class m191219_133854_update_tai_player_treasure
 */
class m191219_133854_update_tai_player_treasure extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%tai_player_treasure}}";
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    $CREATE_SQL="CREATE TRIGGER {{%tai_player_treasure}} AFTER INSERT ON {{%player_treasure}} FOR EACH ROW
BEGIN
DECLARE local_target_id INT;
DECLARE headshoted INT default null;

  CALL add_treasure_stream(NEW.player_id,'treasure',NEW.treasure_id);
  CALL add_player_treasure_hint(NEW.player_id,NEW.treasure_id);
  SET local_target_id=(SELECT target_id FROM treasure WHERE id=NEW.treasure_id);
  SET headshoted=(select true as headshoted FROM target as t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=NEW.player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=NEW.player_id WHERE t.id=local_target_id   GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.treasure_id) AND count(distinct t3.id)=count(distinct t5.finding_id));
  IF headshoted IS NOT NULL THEN
      INSERT INTO headshot (player_id,target_id,created_at) VALUES (NEW.player_id,local_target_id,now());
      INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'headshot',local_target_id,0,'','','','',now());
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
