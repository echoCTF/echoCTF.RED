<?php

use yii\db\Migration;

/**
 * Per player limit of concurrent target instances.
 *
 * Adds `player_spin.max_instances`. New players get the value of the
 * `max_target_instances` sysconfig key (default 1) from the `tai_player`
 * trigger, the same way `perday` gets `spins_per_day`. Subscriptions add their
 * product `max_target_instances` on activation and subtract it when they end.
 *
 * Existing players get the current sysconfig value, plus the product value of
 * their active subscription (if any), so that the subtraction on expiration
 * leaves them with the default.
 */
class m261027_120000_add_max_instances_to_player_spin extends Migration
{
  public $DROP_SQL = "DROP TRIGGER IF EXISTS {{%tai_player}}";

  public $CREATE_SQL = "CREATE TRIGGER {{%tai_player}} AFTER INSERT ON {{%player}} FOR EACH ROW
  thisBegin:BEGIN
    DECLARE ltitle VARCHAR(20) DEFAULT 'Joined the platform';

    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;

    IF (select memc_server_count()<1) THEN
      select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    SELECT memc_set(CONCAT('player_type:',NEW.id), NEW.type) INTO @devnull;
    SELECT memc_set(CONCAT('player:',NEW.id), NEW.id) INTO @devnull;
    IF NEW.id>1 THEN
      INSERT INTO profile (player_id) VALUES (NEW.id);
    ELSE
      INSERT INTO profile (id,player_id) VALUES (NEW.id,NEW.id);
    END IF;
    INSERT INTO player_last (id,on_pui) VALUES (NEW.id,now());
    INSERT INTO player_spin (player_id,counter,total,perday,max_instances,updated_at) values (NEW.id,0,0,memc_get('sysconfig:spins_per_day'),GREATEST(1,IFNULL(memc_get('sysconfig:max_target_instances'),1)),NOW());
    INSERT INTO player_score (player_id) VALUES (NEW.id);
  END";

  public $OLD_CREATE_SQL = "CREATE TRIGGER {{%tai_player}} AFTER INSERT ON {{%player}} FOR EACH ROW
  thisBegin:BEGIN
    DECLARE ltitle VARCHAR(20) DEFAULT 'Joined the platform';

    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;

    IF (select memc_server_count()<1) THEN
      select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    SELECT memc_set(CONCAT('player_type:',NEW.id), NEW.type) INTO @devnull;
    SELECT memc_set(CONCAT('player:',NEW.id), NEW.id) INTO @devnull;
    IF NEW.id>1 THEN
      INSERT INTO profile (player_id) VALUES (NEW.id);
    ELSE
      INSERT INTO profile (id,player_id) VALUES (NEW.id,NEW.id);
    END IF;
    INSERT INTO player_last (id,on_pui) VALUES (NEW.id,now());
    INSERT INTO player_spin (player_id,counter,total,perday,updated_at) values (NEW.id,0,0,memc_get('sysconfig:spins_per_day'),NOW());
    INSERT INTO player_score (player_id) VALUES (NEW.id);
  END";

  public function up()
  {
    $this->addColumn('{{%player_spin}}', 'max_instances', 'TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER [[perday]]');

    // Everyone starts from the current platform default
    $this->execute("UPDATE {{%player_spin}} SET [[max_instances]]=LEAST(255,GREATEST(1,IFNULL((SELECT CAST([[val]] AS UNSIGNED) FROM {{%sysconfig}} WHERE [[id]]='max_target_instances'),1)))");

    // Players with an active subscription also get its product value
    if ($this->db->getTableSchema('{{%player_subscription}}', true) !== null && $this->db->getTableSchema('{{%price}}', true) !== null && $this->db->getTableSchema('{{%product}}', true) !== null) {
      $this->execute("UPDATE {{%player_spin}} ps
        JOIN {{%player_subscription}} s ON s.player_id=ps.player_id AND s.active=1
        JOIN {{%price}} pr ON pr.id=s.price_id
        JOIN {{%product}} p ON p.id=pr.product_id
        SET ps.max_instances=LEAST(255, ps.max_instances + CAST(JSON_UNQUOTE(JSON_EXTRACT(p.metadata,'$.max_target_instances')) AS UNSIGNED))
        WHERE JSON_VALID(p.metadata) AND JSON_EXTRACT(p.metadata,'$.max_target_instances') IS NOT NULL");
    }

    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->OLD_CREATE_SQL)->execute();
    $this->dropColumn('{{%player_spin}}', 'max_instances');
  }
}
