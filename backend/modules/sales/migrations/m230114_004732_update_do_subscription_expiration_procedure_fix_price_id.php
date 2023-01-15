<?php

use yii\db\Migration;

/**
 * Class m230114_004732_update_do_subscription_expiration_procedure_fix_price_id
 */
class m230114_004732_update_do_subscription_expiration_procedure_fix_price_id extends Migration
{
    public $DROP_SQL = "DROP PROCEDURE IF EXISTS {{%do_subscription_expiration}}";
    public $CREATE_SQL = "CREATE PROCEDURE {{%do_subscription_expiration}} ()
    BEGIN
      DECLARE done INT DEFAULT FALSE;
      DECLARE csubscription_id varchar(255);
      DECLARE cprice_id varchar(255);
      DECLARE cproduct_id varchar(40);
      DECLARE cplayer_id INT;
      DECLARE expiredCursor CURSOR FOR SELECT player_id, subscription_id, price_id FROM player_subscription WHERE active=1 AND ending<NOW()-INTERVAL 4 HOUR;
      DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
      OPEN expiredCursor;
      read_loop: LOOP
        FETCH expiredCursor INTO cplayer_id,csubscription_id,cprice_id;
        IF done THEN
          LEAVE read_loop;
        END IF;
        SELECT product_id INTO cproduct_id FROM price WHERE id=cprice_id;
        START TRANSACTION;
          DELETE FROM network_player WHERE player_id=cplayer_id AND network_id IN (SELECT network_id FROM product_network WHERE product_id=cproduct_id);
          UPDATE player_subscription SET active=0 WHERE player_id=cplayer_id AND subscription_id=csubscription_id AND price_id=cprice_id;
        COMMIT;
      END LOOP;
      CLOSE expiredCursor;
    END";
    // Use up()/down() to run migration code without a transaction.
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
