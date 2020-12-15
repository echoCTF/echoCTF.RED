<?php

use yii\db\Migration;

/**
 * Class m201213_130151_update_add_treasure_stream_procedure
 */
class m201213_130151_update_add_treasure_stream_procedure extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%add_treasure_stream}}";
  public $CREATE_SQL="CREATE PROCEDURE add_treasure_stream(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT, IN pts FLOAT)
  BEGIN
    DECLARE ltitle,lpubtitle VARCHAR(255);
    DECLARE lmessage,lpubmessage TEXT;
    DECLARE divider INTEGER DEFAULT 1;
    DECLARE ltid BIGINT;

    SELECT name,pubname,description,pubdescription,target_id INTO ltitle,lpubtitle,lmessage,lpubmessage,ltid FROM treasure WHERE id=recid;
    INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'treasure',recid,pts,ltitle,lmessage,lpubtitle,lpubmessage,now());
  END";

    public function up()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($this->CREATE_SQL)->execute();
    }

    public function down()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
    }
}
