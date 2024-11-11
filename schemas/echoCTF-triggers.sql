SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DELIMITER ;;

DROP TRIGGER IF EXISTS tbd_challenge ;;
CREATE TRIGGER `tbd_challenge` BEFORE DELETE ON `challenge` FOR EACH ROW
thisBegin:BEGIN
IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
END IF;
    DELETE FROM stream WHERE `model`='question' AND model_id IN (SELECT id FROM question WHERE challenge_id=OLD.ID);
    DELETE FROM team_stream WHERE `model`='question' AND model_id IN (SELECT id FROM question WHERE challenge_id=OLD.ID);
END ;;

DROP TRIGGER IF EXISTS tad_challenge ;;
CREATE TRIGGER `tad_challenge` AFTER DELETE ON `challenge` FOR EACH ROW
thisBegin:BEGIN
IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
END IF;
    DELETE FROM stream WHERE `model`='challenge' AND model_id=OLD.id;
    DELETE FROM team_stream WHERE `model`='challenge' AND model_id=OLD.id;
END ;;

DROP TRIGGER IF EXISTS tbi_challenge_solver ;;
CREATE TRIGGER `tbi_challenge_solver` BEFORE INSERT ON `challenge_solver` FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    IF (SELECT count(*) FROM challenge_solver WHERE challenge_id=NEW.challenge_id)=0 THEN
      SET NEW.first=1;
    END IF;
  END ;;

DROP TRIGGER IF EXISTS tai_disabled_route ;;
CREATE TRIGGER `tai_disabled_route` AFTER INSERT ON `disabled_route` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE routes LONGTEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('route', route) ORDER BY route),']') FROM disabled_route ORDER BY route);
      INSERT INTO sysconfig (id,val) VALUES ('disabled_routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
    END ;;

DROP TRIGGER IF EXISTS tau_disabled_route ;;
CREATE TRIGGER `tau_disabled_route` AFTER UPDATE ON `disabled_route` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE routes LONGTEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('route', route) ORDER BY route),']') FROM disabled_route ORDER BY route);
      INSERT INTO sysconfig (id,val) VALUES ('disabled_routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
    END ;;

DROP TRIGGER IF EXISTS tad_disabled_route ;;
CREATE TRIGGER `tad_disabled_route` AFTER DELETE ON `disabled_route` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE routes LONGTEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('route', route) ORDER BY route),']') FROM disabled_route ORDER BY route);
      INSERT INTO sysconfig (id,val) VALUES ('disabled_routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
    END ;;

DROP TRIGGER IF EXISTS tai_finding ;;
CREATE TRIGGER `tai_finding` AFTER INSERT ON `finding` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    DO memc_set(CONCAT('finding:',NEW.protocol,':',ifnull(NEW.port,0), ':', NEW.target_id ),NEW.id);
    UPDATE target_state SET total_findings=total_findings+1,total_points=total_points+IFNULL(NEW.points,0),finding_points=finding_points+IFNULL(NEW.points,0) WHERE id=NEW.target_id;
    END ;;

DROP TRIGGER IF EXISTS tau_finding ;;
CREATE TRIGGER `tau_finding` AFTER UPDATE ON `finding` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;

  IF NEW.protocol != OLD.protocol OR NEW.port!=OLD.port OR NEW.target_id!=OLD.target_id THEN
    SELECT memc_delete(CONCAT('finding:',OLD.protocol,':',ifnull(OLD.port,0), ':', OLD.target_id )) INTO @devnull;
  END IF;
  SELECT memc_set(CONCAT('finding:',NEW.protocol,':',ifnull(NEW.port,0), ':', NEW.target_id ),NEW.id) INTO @devnull;
END ;;

DROP TRIGGER IF EXISTS tad_finding ;;
CREATE TRIGGER `tad_finding` AFTER DELETE ON `finding` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    DO memc_delete(CONCAT('finding:',OLD.protocol,':',ifnull(OLD.port,0), ':', OLD.target_id ));

    UPDATE target_state SET total_findings=total_findings-1,total_points=total_points-IFNULL(OLD.points,0),finding_points=finding_points-IFNULL(OLD.points,0) WHERE id=OLD.target_id;
    DELETE FROM stream WHERE model_id=OLD.id AND model='finding';
    DELETE FROM team_stream WHERE model_id=OLD.id and model='finding';
    END ;;

DROP TRIGGER IF EXISTS tbi_headshot ;;
CREATE TRIGGER `tbi_headshot` BEFORE INSERT ON `headshot` FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    IF (SELECT count(*) FROM headshot WHERE target_id=NEW.target_id)=0 THEN
      SET NEW.first=1;
    END IF;
  END ;;

DROP TRIGGER IF EXISTS tai_headshot ;;
CREATE TRIGGER `tai_headshot` AFTER INSERT ON `headshot` FOR EACH ROW
    thisBegin:BEGIN
    DECLARE private_instance int;
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    SET private_instance=(SELECT COUNT(*) FROM target_instance WHERE player_id=NEW.player_id AND target_id=NEW.target_id);
    IF (SELECT headshot_spin FROM target WHERE id=NEW.target_id)>0 AND private_instance<1 THEN
      INSERT IGNORE INTO spin_queue (target_id, player_id,created_at) VALUES (NEW.target_id,NEW.player_id,NOW());
    ELSEIF private_instance>0 THEN
      UPDATE target_instance SET reboot=2 WHERE player_id=NEW.player_id AND target_id=NEW.target_id;
    END IF;
    IF (SELECT count(*) FROM target_ondemand WHERE target_id=NEW.target_id AND state=1)>0 THEN
        UPDATE target_ondemand SET heartbeat=(NOW() - INTERVAL 59 MINUTE - INTERVAL 30 SECOND) WHERE target_id=NEW.target_id;
    END IF;
    UPDATE target_state SET total_headshots=total_headshots+1,timer_avg=(SELECT ifnull(round(avg(timer)),0) FROM headshot WHERE target_id=NEW.target_id) WHERE id=NEW.target_id;
    END ;;

DROP TRIGGER IF EXISTS tau_headshot ;;
CREATE TRIGGER `tau_headshot` AFTER UPDATE ON `headshot` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    IF (OLD.rating IS NULL AND NEW.rating IS NOT NULL) OR (OLD.rating IS NOT NULL and NEW.rating!=OLD.rating) THEN
      UPDATE target_state SET player_rating=(SELECT round(avg(rating)) FROM headshot WHERE target_id=NEW.target_id AND rating>-1) WHERE id=NEW.target_id;
    END IF;
    IF (OLD.timer IS NULL AND NEW.timer IS NOT NULL) OR (OLD.timer IS NOT NULL AND NEW.timer!=OLD.timer) THEN
        UPDATE target_state SET timer_avg=(SELECT round(avg(timer)) FROM headshot WHERE target_id=NEW.target_id and timer>60) WHERE id=NEW.target_id;
    END IF;
    END ;;

DROP TRIGGER IF EXISTS tad_headshot ;;
CREATE TRIGGER `tad_headshot` AFTER DELETE ON `headshot` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET total_headshots=total_headshots-1,timer_avg=(SELECT ifnull(round(avg(timer)),0) FROM headshot WHERE target_id=OLD.target_id) WHERE id=OLD.target_id;
    DELETE FROM stream WHERE model_id=OLD.target_id and model='headshot';
    END ;;

DROP TRIGGER IF EXISTS tai_network_target ;;
CREATE TRIGGER `tai_network_target` AFTER INSERT ON `network_target` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET on_network=1 WHERE id=NEW.target_id and on_network!=1;
    END ;;

DROP TRIGGER IF EXISTS tad_network_target ;;
CREATE TRIGGER `tad_network_target` AFTER DELETE ON `network_target` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET on_network=ifnull((select 1 from network_target where target_id=OLD.target_id),0) WHERE id=OLD.target_id;
    END ;;

DROP TRIGGER IF EXISTS tai_player ;;
CREATE TRIGGER tai_player AFTER INSERT ON player FOR EACH ROW
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
    INSERT INTO profile (player_id) VALUES (NEW.id);
    INSERT INTO player_last (id,on_pui) VALUES (NEW.id,now());
    INSERT INTO player_spin (player_id,counter,total,perday,updated_at) values (NEW.id,0,0,memc_get('sysconfig:spins_per_day'),NOW());
    INSERT INTO player_score (player_id) VALUES (NEW.id);
  END ;;

DROP TRIGGER IF EXISTS tau_player ;;
CREATE TRIGGER `tau_player` AFTER UPDATE ON `player` FOR EACH ROW
  thisBegin:BEGIN
  DECLARE ltitle VARCHAR(30) DEFAULT "Joined the platform";
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;

  IF NEW.type!=OLD.type THEN
    SELECT memc_set(CONCAT('player_type:',NEW.id), NEW.type) INTO @devnull;
  END IF;

  IF NEW.status=0 AND OLD.status=10 THEN
    INSERT INTO archived_stream SELECT * FROM stream WHERE player_id=NEW.id;
    DELETE FROM stream WHERE player_id=NEW.id;
  ELSEIF NEW.status=10 AND OLD.status=0 THEN
    INSERT INTO stream SELECT * FROM archived_stream WHERE player_id=NEW.id;
    DELETE FROM archived_stream WHERE player_id=NEW.id;
  ELSEIF NEW.status=10 AND (OLD.status=9 OR OLD.status=8) THEN
    DELETE FROM player_token WHERE player_id=NEW.id AND `type`='email_verification';
  END IF;
  END ;;

DROP TRIGGER IF EXISTS tbd_player ;;
CREATE TRIGGER `tbd_player` BEFORE DELETE ON `player` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE tid INT default 0;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SELECT id INTO tid FROM team where owner_id=OLD.id;
      IF tid > 0 THEN
        DELETE FROM team_score WHERE team_id=tid;
      END IF;
      DELETE FROM player_ssl WHERE player_id=OLD.id;
      DELETE FROM player_rank WHERE player_id=OLD.id;
    END ;;

DROP TRIGGER IF EXISTS tad_player ;;
CREATE TRIGGER `tad_player` AFTER DELETE ON `player` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;

      IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
      END IF;
      SELECT memc_delete(CONCAT('player_type:',OLD.id)) INTO @devnull;
      SELECT memc_delete(CONCAT('player:',OLD.id)) INTO @devnull;
      SELECT memc_delete(CONCAT('team_player:',OLD.id)) INTO @devnull;
      DELETE FROM player_score WHERE player_id=OLD.id;
      DELETE FROM player_score_monthly WHERE player_id=OLD.id;
      DELETE FROM player_counter_nf WHERE player_id=OLD.id;
      DELETE FROM profile WHERE player_id=OLD.id;
      DELETE FROM player_last WHERE id=OLD.id;
    END ;;

DROP TRIGGER IF EXISTS tai_player_badge ;;
CREATE TRIGGER `tai_player_badge` AFTER INSERT ON `player_badge` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  CALL add_badge_stream(NEW.player_id,'badge',NEW.badge_id);
END ;;

DROP TRIGGER IF EXISTS tai_player_disabledroute ;;
CREATE TRIGGER `tai_player_disabledroute` AFTER INSERT ON `player_disabledroute` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE routes LONGTEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('player_id',player_id,'route', route) ORDER BY player_id),']') FROM player_disabledroute ORDER BY player_id,route);
      INSERT INTO sysconfig (id,val) VALUES ('player_disabled_routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
    END ;;

DROP TRIGGER IF EXISTS tau_player_disabledroute ;;
CREATE TRIGGER `tau_player_disabledroute` AFTER UPDATE ON `player_disabledroute` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE routes LONGTEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('player_id',player_id,'route', route) ORDER BY player_id),']') FROM player_disabledroute ORDER BY player_id,route);
      INSERT INTO sysconfig (id,val) VALUES ('player_disabled_routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
    END ;;

DROP TRIGGER IF EXISTS tad_player_disabledroute ;;
CREATE TRIGGER `tad_player_disabledroute` AFTER DELETE ON `player_disabledroute` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE routes LONGTEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('player_id',player_id, 'route', route) ORDER BY player_id,route),']') FROM player_disabledroute ORDER BY player_id);
      INSERT INTO sysconfig (id,val) VALUES ('player_disabled_routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
    END ;;

DROP TRIGGER IF EXISTS tai_player_disconnect_queue ;;
CREATE TRIGGER `tai_player_disconnect_queue` AFTER INSERT ON `player_disconnect_queue` FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
    INSERT INTO player_disconnect_queue_history (player_id,created_at) VALUES (NEW.player_id,NOW());
  END ;;

DROP TRIGGER IF EXISTS tad_player_disconnect_queue ;;
CREATE TRIGGER `tad_player_disconnect_queue` AFTER DELETE ON `player_disconnect_queue` FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
    INSERT INTO player_disconnect_queue_history (player_id,created_at) VALUES (OLD.player_id,NOW());
  END ;;

DROP TRIGGER IF EXISTS tbi_player_finding ;;
CREATE TRIGGER `tbi_player_finding` BEFORE INSERT ON `player_finding` FOR EACH ROW
  thisBegin:BEGIN
    DECLARE local_target_id INT;
    DECLARE pts FLOAT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SELECT target_id,points INTO local_target_id,pts FROM finding WHERE id=NEW.finding_id;
    SET NEW.points=pts;
    IF (SELECT count(*) FROM player_target_help WHERE target_id=local_target_id AND player_id=NEW.player_id)>0 THEN
      SET NEW.points=NEW.points/2;
    END IF;
  END ;;

DROP TRIGGER IF EXISTS tai_player_finding ;;
CREATE TRIGGER `tai_player_finding` AFTER INSERT ON `player_finding` FOR EACH ROW
  thisBegin:BEGIN
  DECLARE local_target_id INT;
  DECLARE headshoted INT default null;
  DECLARE min_finding,min_treasure,max_finding,max_treasure, max_val, min_val DATETIME;

  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_set(CONCAT('player_finding:',NEW.player_id, ':', NEW.finding_id),NEW.player_id) INTO @devnull;

  CALL add_finding_stream(NEW.player_id,'finding',NEW.finding_id,NEW.points);
  CALL add_player_finding_hint(NEW.player_id,NEW.finding_id);

  SET local_target_id=(SELECT target_id FROM finding WHERE id=NEW.finding_id);
  SET headshoted=(select true as headshoted FROM target as t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=NEW.player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=NEW.player_id WHERE t.id=local_target_id   GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.treasure_id) AND count(distinct t3.id)=count(distinct t5.finding_id));
  IF headshoted IS NOT NULL THEN
    SELECT min(ts),max(ts) INTO min_finding,max_finding FROM player_finding WHERE player_id=NEW.player_id AND finding_id IN (SELECT id FROM finding WHERE target_id=local_target_id);
    SELECT min(ts),max(ts) INTO min_treasure,max_treasure FROM player_treasure WHERE player_id=NEW.player_id AND treasure_id IN (SELECT id FROM treasure WHERE target_id=local_target_id);
    SELECT GREATEST(max_finding, max_treasure), LEAST(min_finding, min_treasure) INTO max_val,min_val;
    INSERT INTO headshot (player_id,target_id,created_at,timer) VALUES (NEW.player_id,local_target_id,now(),UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val));
    INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'headshot',local_target_id,0,'','','','',now());
  END IF;
  INSERT INTO target_player_state (id,player_id,player_findings,player_points,created_at,updated_at) VALUES (local_target_id,NEW.player_id,1,NEW.points,now(),now()) ON DUPLICATE KEY UPDATE player_findings=player_findings+values(player_findings),player_points=player_points+values(player_points),updated_at=now();
  END ;;

DROP TRIGGER IF EXISTS tad_player_finding ;;
CREATE TRIGGER `tad_player_finding` AFTER DELETE ON `player_finding` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_delete(CONCAT('player_finding:',OLD.player_id, ':', OLD.finding_id)) INTO @devnull;
END ;;

DROP TRIGGER IF EXISTS tau_player_last ;;
CREATE TRIGGER `tau_player_last` AFTER UPDATE ON `player_last` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;

    IF (OLD.vpn_local_address IS NULL AND NEW.vpn_local_address IS NOT NULL) THEN
        DO memc_set(CONCAT('ovpn:',NEW.id),INET_NTOA(NEW.vpn_local_address));
        DO memc_set(CONCAT('ovpn:',INET_NTOA(NEW.vpn_local_address)),NEW.id);
        DO memc_set(CONCAT('ovpn_remote:',NEW.id),INET_NTOA(NEW.vpn_remote_address));
    ELSEIF (OLD.vpn_local_address IS NOT NULL AND NEW.vpn_local_address IS NULL) THEN
        DO memc_delete(CONCAT('ovpn:',NEW.id));
        DO memc_delete(CONCAT('ovpn_remote:',NEW.id));
        DO memc_delete(CONCAT('ovpn:',INET_NTOA(OLD.vpn_local_address)));
    END IF;

    IF (OLD.vpn_local_address IS NULL AND NEW.vpn_local_address IS NOT NULL) OR (OLD.vpn_local_address IS NOT NULL AND NEW.vpn_local_address IS NOT NULL AND NEW.vpn_local_address!=OLD.vpn_local_address) THEN
        INSERT INTO `player_vpn_history` (`player_id`,`vpn_local_address`,`vpn_remote_address`) VALUES (NEW.id,NEW.vpn_local_address,NEW.vpn_remote_address);
    END IF;
    END ;;

DROP TRIGGER IF EXISTS tai_player_question ;;
CREATE TRIGGER `tai_player_question` AFTER INSERT ON `player_question` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE local_challenge_id INT default null;
      DECLARE completed INT default null;
      DECLARE min_question,max_question, max_val, min_val DATETIME default null;

      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;

      IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
      END IF;
      CALL add_stream(NEW.player_id,'question',NEW.question_id);
      SET local_challenge_id=(SELECT challenge_id FROM question WHERE id=NEW.question_id);
      SET completed=(select true as completed FROM challenge as t left join question as t2 on t2.challenge_id=t.id LEFT JOIN player_question as t4 on t4.question_id=t2.id and t4.player_id=NEW.player_id WHERE t.id=local_challenge_id GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.question_id));
      IF completed IS NOT NULL and completed=true THEN
        SELECT min(ts),max(ts) INTO min_question,max_question FROM player_question WHERE player_id=NEW.player_id AND question_id IN (SELECT id FROM question WHERE challenge_id=local_challenge_id);
        SELECT GREATEST(max_question, min_question), LEAST(min_question, max_question) INTO max_val,min_val;
        INSERT IGNORE INTO challenge_solver (player_id,challenge_id,created_at,timer) VALUES (NEW.player_id,local_challenge_id,now(),UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val));
        INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'challenge',local_challenge_id,0,'','','','',now());
      END IF;

    END ;;

DROP TRIGGER IF EXISTS tau_player_score ;;
CREATE TRIGGER `tau_player_score` AFTER UPDATE ON `player_score` FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;

    IF NEW.points>=OLD.points THEN
      INSERT INTO player_score_monthly (player_id, points, dated_at) VALUES (NEW.player_id, ABS(ifnull(OLD.points,0)-NEW.points), EXTRACT(YEAR_MONTH FROM NOW())) ON DUPLICATE KEY UPDATE points=points+values(points);
    END IF;
  END ;;

DROP TRIGGER IF EXISTS tau_player_ssl ;;
CREATE TRIGGER `tau_player_ssl` AFTER UPDATE ON `player_ssl` FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;

    IF OLD.subject!=NEW.subject OR OLD.csr!=NEW.csr OR OLD.crt!=NEW.crt OR OLD.privkey!=NEW.privkey and OLD.subject is not null and OLD.subject!='' THEN
      INSERT INTO `crl` values (NULL,OLD.player_id,OLD.subject,OLD.csr,OLD.crt,OLD.privkey,NOW());
    END IF;
  END ;;

DROP TRIGGER IF EXISTS tad_player_ssl ;;
CREATE TRIGGER `tad_player_ssl` AFTER DELETE ON `player_ssl` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      INSERT INTO `crl` values (NULL,OLD.player_id,OLD.subject,OLD.csr,OLD.crt,OLD.privkey,NOW());
    END ;;

DROP TRIGGER IF EXISTS tai_player_token ;;
CREATE TRIGGER `tai_player_token` AFTER INSERT ON `player_token` FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
    INSERT INTO player_token_history (player_id,`type`,token,`description`,expires_at,created_at,ts) VALUES (NEW.player_id,NEW.type,NEW.token,NEW.description,NEW.expires_at,NEW.created_at,NOW());
  END ;;

DROP TRIGGER IF EXISTS tau_player_token ;;
CREATE TRIGGER `tau_player_token` AFTER UPDATE ON `player_token` FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
  IF (NEW.token != OLD.token) THEN
    INSERT INTO player_token_history (player_id,`type`,token,description,expires_at,created_at,ts) VALUES (NEW.player_id,NEW.type,NEW.token,NEW.description,NEW.expires_at,NEW.created_at,NOW());
  END IF;
  END ;;

DROP TRIGGER IF EXISTS tad_player_token ;;
CREATE TRIGGER `tad_player_token` AFTER DELETE ON `player_token` FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
  INSERT INTO player_token_history (player_id,`type`,token,description,expires_at,created_at,ts) VALUES (OLD.player_id,OLD.type,OLD.token,OLD.description,OLD.expires_at,OLD.created_at,NOW());
  END ;;

DROP TRIGGER IF EXISTS tbi_player_treasure ;;
CREATE TRIGGER `tbi_player_treasure` BEFORE INSERT ON `player_treasure` FOR EACH ROW
  thisBegin:BEGIN
    DECLARE local_target_id INT;
    DECLARE pts FLOAT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SELECT target_id,points INTO local_target_id,pts FROM treasure WHERE id=NEW.treasure_id;
    SET NEW.points=pts;
    IF (SELECT count(*) FROM player_target_help WHERE target_id=local_target_id AND player_id=NEW.player_id)>0 THEN
      SET NEW.points=NEW.points/2;
    END IF;
  END ;;

DROP TRIGGER IF EXISTS tai_player_treasure ;;
CREATE TRIGGER `tai_player_treasure` AFTER INSERT ON `player_treasure` FOR EACH ROW
    thisBegin:BEGIN
    DECLARE local_target_id INT;
    DECLARE headshoted INT default null;
    DECLARE min_finding,min_treasure,max_finding,max_treasure, max_val, min_val DATETIME;

    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;

    CALL add_treasure_stream(NEW.player_id,'treasure',NEW.treasure_id,NEW.points);
    CALL add_player_treasure_hint(NEW.player_id,NEW.treasure_id);

    SET local_target_id=(SELECT target_id FROM treasure WHERE id=NEW.treasure_id);
    SET headshoted=(select true as headshoted FROM target as t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=NEW.player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=NEW.player_id WHERE t.id=local_target_id   GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.treasure_id) AND count(distinct t3.id)=count(distinct t5.finding_id));

    IF headshoted IS NOT NULL THEN
        SELECT min(ts),max(ts) INTO min_finding,max_finding FROM player_finding WHERE player_id=NEW.player_id AND finding_id IN (SELECT id FROM finding WHERE target_id=local_target_id);
        SELECT min(ts),max(ts) INTO min_treasure,max_treasure FROM player_treasure WHERE player_id=NEW.player_id AND treasure_id IN (SELECT id FROM treasure WHERE target_id=local_target_id);
        SELECT GREATEST(max_finding, max_treasure), LEAST(min_finding, min_treasure) INTO max_val,min_val;
        INSERT INTO headshot (player_id,target_id,created_at,timer) VALUES (NEW.player_id,local_target_id,now(),UNIX_TIMESTAMP(max_val)-UNIX_TIMESTAMP(min_val));
        INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.player_id,'headshot',local_target_id,0,'','','','',now());
    END IF;
    INSERT INTO target_player_state (id,player_id,player_treasures,player_points,created_at,updated_at) VALUES (local_target_id,NEW.player_id,1,NEW.points,now(),now()) ON DUPLICATE KEY UPDATE player_treasures=player_treasures+values(player_treasures),player_points=player_points+values(player_points),updated_at=now();
    END ;;

DROP TRIGGER IF EXISTS tbi_profile ;;
CREATE TRIGGER `tbi_profile` BEFORE INSERT ON `profile` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF NEW.id is NULL or NEW.id<10000000 THEN
    REPEAT
        SET NEW.id=round(rand()*10000000);
    UNTIL (SELECT id FROM profile WHERE id=NEW.id) IS NULL
    END REPEAT;
  END IF;
  IF NEW.created_at IS NULL or NEW.updated_at IS NULL THEN
    SET NEW.created_at=NOW();
    SET NEW.updated_at=NOW();
  END IF;
  IF NEW.bio IS NULL THEN
    SET NEW.bio='No bio...';
  END IF;
END ;;

DROP TRIGGER IF EXISTS tad_sessions ;;
CREATE TRIGGER `tad_sessions` AFTER DELETE ON `sessions` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;

  SELECT memc_delete(CONCAT('memc.sess.',OLD.id)) INTO @devnull;
  SELECT memc_delete(CONCAT('player_session:',OLD.player_id)) INTO @devnull;
END ;;

DROP TRIGGER IF EXISTS tad_spin_queue ;;
CREATE TRIGGER `tad_spin_queue` AFTER DELETE ON `spin_queue` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;
  INSERT INTO `spin_history` (target_id,player_id,created_at,updated_at) VALUES (OLD.target_id,OLD.player_id,OLD.created_at,NOW());
END ;;

DROP TRIGGER IF EXISTS tai_stream ;;
CREATE TRIGGER `tai_stream` AFTER INSERT ON `stream` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE lteam_id INT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      IF NEW.points>0 THEN
        INSERT INTO player_score (player_id,points) VALUES (NEW.player_id,NEW.points) ON DUPLICATE KEY UPDATE points=points+values(points);
      END IF;
      SELECT team_id INTO lteam_id FROM team_player WHERE player_id=NEW.player_id AND approved=1;
      IF lteam_id IS NOT NULL THEN
        INSERT IGNORE INTO team_stream (team_id,model,model_id,points,ts) VALUES (lteam_id,NEW.model,NEW.model_id,NEW.points,NEW.ts);
      END IF;
    END ;;

DROP TRIGGER IF EXISTS tad_stream ;;
CREATE TRIGGER `tad_stream` AFTER DELETE ON `stream` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    INSERT INTO player_score (player_id,points) VALUES (OLD.player_id,-OLD.points) ON DUPLICATE KEY UPDATE points=if(points+values(points)<0,0,points+values(points));
    END ;;

DROP TRIGGER IF EXISTS tai_sysconfig ;;
CREATE TRIGGER `tai_sysconfig` AFTER INSERT ON `sysconfig` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
          LEAVE thisBegin;
      END IF;

      IF (select memc_server_count()<1) THEN
        select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
      END IF;
      DO memc_set('sysconfig_json',(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('id', id,'val',val) ORDER BY id),']') FROM sysconfig WHERE id NOT LIKE 'CA%' and id NOT IN ('disabled_routes','frontpage_scenario','routes','writeup_rules','vpn-ta.key') ORDER BY id));
      DO memc_set(CONCAT('sysconfig:',NEW.id),NEW.val);
    END ;;

DROP TRIGGER IF EXISTS tau_sysconfig ;;
CREATE TRIGGER `tau_sysconfig` AFTER UPDATE ON `sysconfig` FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    IF (select memc_server_count()<1) THEN
      select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    IF NEW.id != OLD.id THEN
      SELECT memc_delete(CONCAT('sysconfig:',OLD.id)) INTO @devnull;
    END IF;
    DO memc_set('sysconfig_json',(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('id', id,'val',val) ORDER BY id),']') FROM sysconfig WHERE id NOT LIKE 'CA%' and id NOT IN ('disabled_routes','frontpage_scenario','routes','writeup_rules','vpn-ta.key') ORDER BY id));
    DO memc_set(CONCAT('sysconfig:',NEW.id),NEW.val);
  END ;;

DROP TRIGGER IF EXISTS tad_sysconfig ;;
CREATE TRIGGER `tad_sysconfig` AFTER DELETE ON `sysconfig` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
          LEAVE thisBegin;
      END IF;

    IF (select memc_server_count()<1) THEN
      select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
    END IF;
    DO memc_set('sysconfig_json',(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('id', id,'val',val) ORDER BY id),']') FROM sysconfig WHERE id NOT LIKE 'CA%' and id NOT IN ('disabled_routes','frontpage_scenario','routes','writeup_rules','vpn-ta.key') ORDER BY id));
    DO memc_delete(CONCAT('sysconfig:',OLD.id));
  END ;;

DROP TRIGGER IF EXISTS tai_target ;;
CREATE TRIGGER `tai_target` AFTER INSERT ON `target` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;

    IF (select memc_server_count()<1) THEN
        DO memc_servers_set('127.0.0.1');
    END IF;
    INSERT IGNORE INTO target_state (id) values (NEW.id);
    DO memc_set(CONCAT('target:',NEW.id),NEW.ip);
    DO memc_set(CONCAT('target:',NEW.ip),NEW.id);
    END ;;

DROP TRIGGER IF EXISTS tbd_target ;;
CREATE TRIGGER `tbd_target` BEFORE DELETE ON `target` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
        DELETE FROM headshot WHERE target_id=OLD.id;
        DELETE FROM finding WHERE target_id=OLD.id;
        DELETE FROM treasure WHERE target_id=OLD.id;
        DELETE FROM target_state WHERE id=OLD.id;
    END ;;

DROP TRIGGER IF EXISTS tai_target_instance ;;
CREATE TRIGGER `tai_target_instance` AFTER INSERT ON `target_instance` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      IF NEW.ip IS NOT NULL THEN
        DO memc_set(CONCAT('target:',NEW.ip),NEW.target_id);
      END IF;
      INSERT DELAYED INTO `target_instance_audit` (op,player_id,target_id,server_id,ip,reboot,team_allowed,ts) VALUES ('i',NEW.player_id,NEW.target_id,NEW.server_id,NEW.ip,NEW.reboot,NEW.team_allowed,NOW());
    END ;;

DROP TRIGGER IF EXISTS tau_target_instance ;;
CREATE TRIGGER `tau_target_instance` AFTER UPDATE ON `target_instance` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      IF NEW.ip IS NOT NULL AND OLD.ip IS NULL THEN
        DO memc_set(CONCAT('target:',NEW.ip),NEW.target_id);
        INSERT DELAYED INTO `target_instance_audit` (op,player_id,target_id,server_id,ip,reboot,team_allowed,ts) VALUES ('u',NEW.player_id,NEW.target_id,NEW.server_id,NEW.ip,NEW.reboot,NEW.team_allowed,NOW());
      ELSEIF (NEW.ip IS NULL OR NEW.ip = '') and OLD.ip IS NOT NULL THEN
          DO memc_delete(CONCAT('target:',OLD.ip));
          INSERT DELAYED INTO `target_instance_audit` (op,player_id,target_id,server_id,ip,reboot,team_allowed,ts) VALUES ('u',NEW.player_id,NEW.target_id,NEW.server_id,NEW.ip,NEW.reboot,NEW.team_allowed,NOW());
      ELSEIF NEW.ip!=OLD.ip THEN
        DO memc_delete(CONCAT('target:',OLD.ip));
        DO memc_set(CONCAT('target:',NEW.ip),NEW.target_id);
        INSERT DELAYED INTO `target_instance_audit` (op,player_id,target_id,server_id,ip,reboot,team_allowed,ts) VALUES ('u',NEW.player_id,NEW.target_id,NEW.server_id,NEW.ip,NEW.reboot,NEW.team_allowed,NOW());
      END IF;
    END ;;

DROP TRIGGER IF EXISTS tad_target_instance ;;
CREATE TRIGGER `tad_target_instance` AFTER DELETE ON `target_instance` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      IF OLD.ip IS NOT NULL THEN
        DO memc_delete(CONCAT('target:',OLD.ip));
      END IF;
      INSERT DELAYED INTO `target_instance_audit` (op,player_id,target_id,server_id,ip,reboot,team_allowed,ts) VALUES ('d',OLD.player_id,OLD.target_id,OLD.server_id,OLD.ip,OLD.reboot,OLD.team_allowed,NOW());
    END ;;

DROP TRIGGER IF EXISTS tai_target_ondemand ;;
CREATE TRIGGER `tai_target_ondemand` AFTER INSERT ON `target_ondemand` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET on_ondemand=1,ondemand_state=NEW.state WHERE id=NEW.target_id;
    END ;;

DROP TRIGGER IF EXISTS tau_target_ondemand ;;
CREATE TRIGGER `tau_target_ondemand` AFTER UPDATE ON `target_ondemand` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;

    UPDATE target_state SET ondemand_state=NEW.state WHERE id=NEW.target_id;

    END ;;

DROP TRIGGER IF EXISTS tad_target_ondemand ;;
CREATE TRIGGER `tad_target_ondemand` AFTER DELETE ON `target_ondemand` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET on_ondemand=0,ondemand_state=-1 WHERE id=OLD.target_id;
    END ;;

DROP TRIGGER IF EXISTS tai_team ;;
CREATE TRIGGER `tai_team` AFTER INSERT ON `team` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.id,NEW.owner_id,'create',CONCAT('Team ',NEW.name,' created'));
    END ;;

DROP TRIGGER IF EXISTS tau_team ;;
CREATE TRIGGER `tau_team` AFTER UPDATE ON `team` FOR EACH ROW
    thisBegin:BEGIN
      DECLARE msg TEXT;
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      SET msg = 'Team details updated:';
      IF OLD.name != NEW.name THEN
        SET msg = CONCAT(msg,' name=',NEW.name);
      END IF;

      IF OLD.recruitment IS NOT NULL OR OLD.recruitment != NEW.recruitment THEN
        SET msg = CONCAT(msg,' recruitment=',NEW.recruitment);
      END IF;

      IF OLD.description != NEW.description THEN
        SET msg = CONCAT(msg,' description=',NEW.description);
      END IF;

      IF OLD.inviteonly != NEW.inviteonly THEN
        SET msg = CONCAT(msg,' inviteonly=',NEW.inviteonly);
      END IF;

      IF OLD.token != NEW.token THEN
        SET msg = CONCAT(msg,' token=',NEW.token);
      END IF;

      INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.id,NEW.owner_id,'update',msg);
    END ;;

DROP TRIGGER IF EXISTS tad_team ;;
CREATE TRIGGER `tad_team` AFTER DELETE ON `team` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      DELETE FROM `team_stream` WHERE team_id=OLD.id;
      DELETE FROM `team_rank` WHERE team_id=OLD.id;
      DELETE FROM `team_score` WHERE team_id=OLD.id;
      INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (OLD.id,OLD.owner_id,'delete',CONCAT('Team ',OLD.name,' deleted'));
    END ;;

DROP TRIGGER IF EXISTS tai_team_player ;;
CREATE TRIGGER `tai_team_player` AFTER INSERT ON `team_player` FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.team_id,NEW.player_id,'join','Player joined the team');
  END ;;

DROP TRIGGER IF EXISTS tau_team_player ;;
CREATE TRIGGER `tau_team_player` AFTER UPDATE ON `team_player` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      IF OLD.approved != NEW.approved THEN
        IF NEW.approved = 0 THEN
          INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.team_id,NEW.player_id,'reject','Player membership rejected');
        ELSE
          INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (NEW.team_id,NEW.player_id,'approve','Player membership approved');
        END IF;
      END IF;
    END ;;

DROP TRIGGER IF EXISTS tad_team_player ;;
CREATE TRIGGER `tad_team_player` AFTER DELETE ON `team_player` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      INSERT INTO team_audit (team_id,player_id,`action`,`message`) VALUES (OLD.team_id,OLD.player_id,'withdraw','Player removed from the team');
    END ;;

DROP TRIGGER IF EXISTS tai_team_stream ;;
CREATE TRIGGER `tai_team_stream` AFTER INSERT ON `team_stream` FOR EACH ROW
  thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    INSERT INTO `team_score` (`team_id`,`points`,`ts`) VALUES (NEW.team_id,NEW.points,NEW.ts) ON DUPLICATE KEY UPDATE points=points+values(points),ts=values(ts);
  END ;;

DROP TRIGGER IF EXISTS tad_team_stream ;;
CREATE TRIGGER `tad_team_stream` AFTER DELETE ON `team_stream` FOR EACH ROW
    thisBegin:BEGIN
      IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
      END IF;
      INSERT INTO `team_score` (`team_id`,`points`,`ts`) VALUES (OLD.team_id,-OLD.points,OLD.ts) ON DUPLICATE KEY UPDATE points=if(points+values(points)<0,0,points+values(points)),ts=values(ts);
    END ;;

DROP TRIGGER IF EXISTS tai_treasure ;;
CREATE TRIGGER `tai_treasure` AFTER INSERT ON `treasure` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET total_treasures=total_treasures+1,total_points=total_points+ifnull(NEW.points,0),treasure_points=treasure_points+ifnull(NEW.points,0) WHERE id=NEW.target_id;
    END ;;

DROP TRIGGER IF EXISTS tad_treasure ;;
CREATE TRIGGER `tad_treasure` AFTER DELETE ON `treasure` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;
    UPDATE target_state SET total_treasures=total_treasures-1,total_points=total_points-IFNULL(OLD.points,0),treasure_points=treasure_points-IFNULL(OLD.points,0) WHERE id=OLD.target_id;
    DELETE FROM stream WHERE model_id=OLD.id and model='treasure';
    DELETE FROM team_stream WHERE model_id=OLD.id and model='treasure';
    END ;;

DROP TRIGGER IF EXISTS tai_url_route ;;
CREATE TRIGGER `tai_url_route` AFTER INSERT ON `url_route` FOR EACH ROW
  thisBegin:BEGIN
    DECLARE routes LONGTEXT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('source', source, 'destination', destination) ORDER BY weight,source,destination),']') FROM url_route ORDER BY weight, source, destination);
    INSERT INTO sysconfig (id,val) VALUES ('routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
  END ;;

DROP TRIGGER IF EXISTS tau_url_route ;;
CREATE TRIGGER `tau_url_route` AFTER UPDATE ON `url_route` FOR EACH ROW
  thisBegin:BEGIN
    DECLARE routes LONGTEXT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('source', source, 'destination', destination)  ORDER BY weight,source,destination),']') FROM url_route  ORDER BY weight, source, destination);
    INSERT INTO sysconfig (id,val) VALUES ('routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
  END ;;

DROP TRIGGER IF EXISTS tad_url_route ;;
CREATE TRIGGER `tad_url_route` AFTER DELETE ON `url_route` FOR EACH ROW
  thisBegin:BEGIN
    DECLARE routes LONGTEXT;
    IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
    END IF;
    SET routes:=(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('source', source, 'destination', destination) ORDER BY weight,source,destination),']') FROM url_route ORDER BY weight, source, destination);
    INSERT INTO sysconfig (id,val) VALUES ('routes',routes) ON DUPLICATE KEY UPDATE val=VALUES(val);
  END ;;

DROP TRIGGER IF EXISTS tai_writeup ;;
CREATE TRIGGER `tai_writeup` AFTER INSERT ON `writeup` FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
    UPDATE target_state SET total_writeups=total_writeups+1, approved_writeups=approved_writeups+IF(NEW.approved>0,1,0) WHERE id=NEW.target_id;
  END ;;

DROP TRIGGER IF EXISTS tau_writeup ;;
CREATE TRIGGER `tau_writeup` AFTER UPDATE ON `writeup` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;

    IF NEW.approved=1 and OLD.approved=0 THEN
       UPDATE target_state SET approved_writeups=approved_writeups+1 WHERE id=NEW.target_id;
    ELSEIF NEW.approved=0 and OLD.approved=1 THEN
       UPDATE target_state SET approved_writeups=approved_writeups-1 WHERE id=NEW.target_id;
    END IF;

    END ;;

DROP TRIGGER IF EXISTS tad_writeup ;;
CREATE TRIGGER `tad_writeup` AFTER DELETE ON `writeup` FOR EACH ROW
    thisBegin:BEGIN
    IF (@TRIGGER_CHECKS = FALSE) THEN
        LEAVE thisBegin;
    END IF;

    IF OLD.approved=1 THEN
       UPDATE target_state SET approved_writeups=approved_writeups-1,total_writeups=total_writeups-1 WHERE id=OLD.target_id;
    ELSEIF OLD.approved=0 THEN
       UPDATE target_state SET total_writeups=total_writeups-1 WHERE id=OLD.target_id;
    END IF;

    END ;;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
