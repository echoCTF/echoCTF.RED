<?php

use yii\db\Migration;

/**
 * Class m241103_121625_create_expire_player_tokens_procedure
 */
class m241103_121625_create_expire_player_tokens_procedure extends Migration
{
  public $DROP_SQL = "DROP PROCEDURE IF EXISTS {{%expire_player_tokens}}";
  public $CREATE_SQL = "CREATE PROCEDURE {{%expire_player_tokens}} ()
  BEGIN
  DECLARE tnow TIMESTAMP;
  SET tnow=NOW();
  IF (SELECT COUNT(*) FROM player_token WHERE expires_at<tnow)>0 THEN
    START TRANSACTION;
    INSERT INTO notification (player_id,category,title,body,archived,created_at,updated_at) SELECT player_id,'info','Token expiration',CONCAT(type,' Token [',description,'] expired at ',expires_at),0,tnow,tnow FROM player_token WHERE expires_at<tnow;
    DELETE FROM player_token WHERE expires_at<tnow;
    COMMIT;
  END IF;
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
