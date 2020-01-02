<?php

use yii\db\Migration;

/**
 * Class m200102_113523_update_add_finding_stream_routine
 */
class m200102_113523_update_add_finding_stream_routine extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%add_finding_stream}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%add_finding_stream}}(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT)
    BEGIN
      DECLARE ltitle,lpubtitle VARCHAR(255);
      DECLARE lmessage,lpubmessage TEXT;
      DECLARE pts BIGINT;
      SELECT name,pubname,description,pubdescription,points INTO ltitle,lpubtitle,lmessage,lpubmessage,pts FROM finding WHERE id=recid;
    	INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'finding',recid,pts,ltitle,lmessage,lpubtitle,lpubmessage,now());
    END";

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($this->CREATE_SQL)->execute();

    }

    public function down()
    {
        echo "m200102_113523_update_add_finding_stream_routine cannot be reverted.\n";

    }
}
