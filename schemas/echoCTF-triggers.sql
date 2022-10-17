/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

DELIMITER ;;

DROP TRIGGER IF EXISTS `tau_report` ;;
--
-- When a new finding is added, update memcached with the finding details
--
DROP TRIGGER IF EXISTS `tai_finding` ;;
CREATE TRIGGER `tai_finding` AFTER INSERT ON `finding` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_set(CONCAT('finding:',NEW.protocol,':',ifnull(NEW.port,0), ':', NEW.target_id ),NEW.id) INTO @devnull;
END ;;

--
-- When a finding is updated also update the relevant memcached values
--
DROP TRIGGER IF EXISTS `tau_finding` ;;
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

--
--  Ensure we remove the finding details from memcached when a
--  finding is removed
--
DROP TRIGGER IF EXISTS `tad_finding` ;;
CREATE TRIGGER `tad_finding` AFTER DELETE ON `finding` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_delete(CONCAT('finding:',OLD.protocol,':',ifnull(OLD.port,0), ':', OLD.target_id )) INTO @devnull;
END ;;


DROP TRIGGER IF EXISTS `tai_player` ;;
CREATE TRIGGER `tai_player` AFTER INSERT ON `player` FOR EACH ROW
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
  INSERT INTO player_spin (player_id,counter,total,updated_at) values (NEW.id,0,0,NOW());
  INSERT INTO player_score (player_id) VALUES (NEW.id);
--  IF NEW.active=1 THEN
--    INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.id,'user',NEW.id,0,ltitle,ltitle,ltitle,ltitle,now());
--  END IF;
END ;;

DROP TRIGGER IF EXISTS `tbu_player` ;;
CREATE TRIGGER `tbu_player` BEFORE UPDATE ON `player` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (NEW.status!=OLD.status AND NEW.status=10) THEN
    SET NEW.verification_token=NULL;
  END IF;
END ;;

DROP TRIGGER IF EXISTS `tau_player` ;;
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
--  IF (NEW.active!=OLD.active AND NEW.active=1) THEN
--    INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (NEW.id,'user',NEW.id,0,ltitle,ltitle,ltitle,ltitle,now());
--  END IF;
END ;;


DROP TRIGGER IF EXISTS `tbd_player` ;;
CREATE TRIGGER `tbd_player` BEFORE DELETE ON `player` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  DELETE FROM player_ssl WHERE player_id=OLD.id;
  DELETE FROM player_rank WHERE player_id=OLD.id;
END ;;


DROP TRIGGER IF EXISTS `tad_player` ;;
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
  DELETE FROM profile WHERE player_id=OLD.id;
  DELETE FROM player_last WHERE id=OLD.id;
END ;;


DROP TRIGGER IF EXISTS `tai_player_badge` ;;
CREATE TRIGGER `tai_player_badge` AFTER INSERT ON `player_badge` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  CALL add_badge_stream(NEW.player_id,'badge',NEW.badge_id);
END ;;

DROP TRIGGER IF EXISTS `tai_player_finding` ;;
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
  CALL add_finding_stream(NEW.player_id,'finding',NEW.finding_id);
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
END ;;


--
-- Keep track of changes on player_last. This is needed in order to be able
-- to troubleshoot and investigate problems
--
DROP TRIGGER IF EXISTS `tau_player_last` ;;
CREATE TRIGGER `tau_player_last` AFTER UPDATE ON `player_last` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (OLD.vpn_local_address IS NULL AND NEW.vpn_local_address IS NOT NULL) OR (OLD.vpn_local_address IS NOT NULL AND NEW.vpn_local_address IS NOT NULL AND NEW.vpn_local_address!=OLD.vpn_local_address) THEN
    INSERT INTO `player_vpn_history` (`player_id`,`vpn_local_address`,`vpn_remote_address`) VALUES (NEW.id,NEW.vpn_local_address,NEW.vpn_remote_address);
  END IF;
END ;;


--
-- Add a stream notification for the answered question
--
DROP TRIGGER IF EXISTS `tai_player_question` ;;
CREATE TRIGGER `tai_player_question` AFTER INSERT ON `player_question` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;
  CALL add_stream(NEW.player_id,'question',NEW.question_id);
END ;;

--
-- On any change of the SSL record of the user, update the CRL list with the
-- past values so that we can revoke the old keys
--
DROP TRIGGER IF EXISTS `tau_player_ssl` ;;
CREATE TRIGGER `tau_player_ssl` AFTER UPDATE ON `player_ssl` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF OLD.subject!=NEW.subject OR OLD.csr!=NEW.csr OR OLD.crt!=NEW.crt OR OLD.privkey!=NEW.privkey and OLD.subject is not null and OLD.subject!='' THEN
    INSERT INTO `crl` values (NULL,OLD.player_id,OLD.subject,OLD.csr,OLD.crt,OLD.txtcrt,OLD.privkey,NOW());
  END IF;
END ;;


DROP TRIGGER IF EXISTS `tad_player_ssl` ;;
CREATE TRIGGER `tad_player_ssl` AFTER DELETE ON `player_ssl` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;
  INSERT INTO `crl` values (NULL,OLD.player_id,OLD.subject,OLD.csr,OLD.crt,OLD.txtcrt,OLD.privkey,NOW());
END ;;


DROP TRIGGER IF EXISTS `tai_player_treasure` ;;
CREATE TRIGGER `tai_player_treasure` AFTER INSERT ON `player_treasure` FOR EACH ROW
thisBegin:BEGIN
  DECLARE local_target_id INT;
  DECLARE headshoted INT default null;
  DECLARE min_finding,min_treasure,max_finding,max_treasure, max_val, min_val DATETIME;

  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  CALL add_treasure_stream(NEW.player_id,'treasure',NEW.treasure_id);
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
END ;;


DROP TRIGGER IF EXISTS `tbi_profile` ;;
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



DROP TRIGGER IF EXISTS `tad_spin_queue` ;;
CREATE TRIGGER `tad_spin_queue` AFTER DELETE ON `spin_queue` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;
  INSERT INTO `spin_history` (target_id,player_id,created_at,updated_at) VALUES (OLD.target_id,OLD.player_id,OLD.created_at,NOW());
END ;;

-- XXXFIXMEXXX This needs to be optimised with an UPDATE instead of INSERT and
-- only when points>0
DROP TRIGGER IF EXISTS `tai_stream` ;;
CREATE TRIGGER `tai_stream` AFTER INSERT ON stream FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  INSERT INTO player_score (player_id,points) VALUES (NEW.player_id,NEW.points) ON DUPLICATE KEY UPDATE points=points+values(points);
  IF (SELECT count(team_id) FROM team_player WHERE player_id=NEW.player_id)>0 THEN
    INSERT INTO team_score (team_id,points) VALUES ((SELECT team_id FROM team_player WHERE player_id=NEW.player_id),NEW.points) ON DUPLICATE KEY UPDATE points=points+values(points);
  END IF;
END ;;

DROP TRIGGER IF EXISTS `tai_sysconfig` ;;
CREATE TRIGGER `tai_sysconfig` AFTER INSERT ON `sysconfig` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_set(CONCAT('sysconfig:',NEW.id),NEW.val) INTO @devnull;
END ;;

DROP TRIGGER IF EXISTS `tau_sysconfig` ;;
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
  SELECT memc_set(CONCAT('sysconfig:',NEW.id),NEW.val) INTO @devnull;
END ;;


DROP TRIGGER IF EXISTS `tad_sysconfig` ;;
CREATE TRIGGER `tad_sysconfig` AFTER DELETE ON `sysconfig` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_delete(CONCAT('sysconfig:',OLD.id)) INTO @devnull;
END ;;

-- XXXFIXMEXXX ONLY UPDATE ACTIVE TARGETS
DROP TRIGGER IF EXISTS `tai_target` ;;
CREATE TRIGGER `tai_target` AFTER INSERT ON `target` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_set(CONCAT('target:',NEW.id),NEW.ip) INTO @devnull;
  SELECT memc_set(CONCAT('target:',NEW.ip),NEW.id) INTO @devnull;
END ;;

-- Schedule spin of target when it gets headshoted
DROP TRIGGER IF EXISTS `tai_headshot` ;;
CREATE TRIGGER `tai_headshot` AFTER INSERT ON `headshot` FOR EACH ROW
thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
    LEAVE thisBegin;
  END IF;
  INSERT IGNORE INTO spin_queue (target_id, player_id,created_at) VALUES (NEW.target_id,NEW.player_id,NOW());
END ;;

DROP TRIGGER IF EXISTS `tad_player_finding` ;;
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


DROP TRIGGER IF EXISTS `tad_sessions` ;;
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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
