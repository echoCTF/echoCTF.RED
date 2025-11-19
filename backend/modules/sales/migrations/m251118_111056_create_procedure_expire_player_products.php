<?php

use yii\db\Migration;

class m251118_111056_create_procedure_expire_player_products extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%expire_player_products}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%expire_player_products}}()
  BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE rid,lplayer_id INT;
    DECLARE lprice_id VARCHAR(64);
    DECLARE product_name VARCHAR(256);
    DECLARE notification_body text;
    DECLARE notification_title text;
    DECLARE cur CURSOR FOR SELECT id,player_id,price_id FROM player_product WHERE ending < NOW() - INTERVAL 1 DAY;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;
    read_loop: LOOP
      FETCH cur INTO rid,lplayer_id,lprice_id;
      IF done THEN
        LEAVE read_loop;
      END IF;
      SELECT product.name INTO product_name FROM price LEFT JOIN product on price.product_id=product.id WHERE price.id=lprice_id;
      set notification_title='{product_name} expired';
      set notification_body='Your {product_name} has expired. Feel free to get a new one any time. Thank you for your time';
      INSERT INTO notification (player_id,category,title,body,created_at) VALUES (lplayer_id,'swal:info',REPLACE(notification_title,'{product_name}',product_name),REPLACE(notification_body,'{product_name}',product_name),NOW());
      DELETE FROM player_product WHERE id=rid;
    END LOOP;
    CLOSE cur;

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