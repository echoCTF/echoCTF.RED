<?php

use yii\db\Migration;

/**
 * Class m200102_113545_update_add_treasure_stream_routine
 */
class m200102_113545_update_add_treasure_stream_routine extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%add_treasure_stream}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%add_treasure_stream}}(IN usid BIGINT, IN tbl VARCHAR(255), IN recid INT)
    BEGIN
    	DECLARE ltitle,lpubtitle VARCHAR(255);
    	DECLARE lmessage,lpubmessage TEXT;
    	DECLARE divider INTEGER DEFAULT 1;
    	DECLARE pts BIGINT;
    	SELECT name,pubname,description,pubdescription,points INTO ltitle,lpubtitle,lmessage,lpubmessage,pts FROM treasure WHERE id=recid;
    	INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (usid,'treasure',recid,pts,ltitle,lmessage,lpubtitle,lpubmessage,now());
    END";

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
      $this->db->createCommand($this->DROP_SQL)->execute();
      $this->db->createCommand($this->CREATE_SQL)->execute();

    }

    public function down()
    {
        echo "m200102_113545_update_add_treasure_stream_routine cannot be reverted.\n";

    }

}
