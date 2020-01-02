<?php

use yii\db\Migration;

/**
 * Class m200102_113443_update_add_badge_stream_routine
 */
class m200102_113443_update_add_badge_stream_routine extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%add_badge_stream}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%add_badge_stream}}(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT)
BEGIN
  DECLARE ltitle,lpubtitle VARCHAR(255);
  DECLARE lmessage,lpubmessage TEXT;
  DECLARE pts BIGINT;
  SELECT name,pubname,description,pubdescription,points INTO ltitle,lpubtitle,lmessage,lpubmessage,pts FROM badge WHERE id=recid;
  INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'badge',recid,pts,ltitle,lmessage,lpubtitle,lpubmessage,now());
END";

    public function up()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($this->CREATE_SQL)->execute();

    }

    public function down()
    {
        echo "m200102_113443_update_add_badge_stream_routine cannot be reverted.\n";

    }
}
