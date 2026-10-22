SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DELIMITER ;;

--
-- Dumping events for database 'echoCTF'
--
DROP EVENT IF EXISTS `ev_player_token_expiration` ;;
CREATE EVENT `ev_player_token_expiration` ON SCHEDULE EVERY 10 SECOND STARTS '2024-11-06 12:26:52' ON COMPLETION PRESERVE ENABLE DO BEGIN
  IF GET_LOCK('ev_player_token_expiration', 0) = 1 THEN
    CALL expire_player_tokens();
    DO RELEASE_LOCK('ev_player_token_expiration');
  END IF;
END ;;

DROP EVENT IF EXISTS `player_maintenance` ;;
CREATE EVENT `player_maintenance` ON SCHEDULE EVERY 1 DAY STARTS '2020-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
  CALL player_maintenance();
END ;;

DROP EVENT IF EXISTS `rotate_notifications` ;;
CREATE EVENT `rotate_notifications` ON SCHEDULE EVERY 12 HOUR STARTS '2023-04-03 00:00:01' ON COMPLETION PRESERVE ENABLE DO BEGIN
  IF GET_LOCK('rotate_notifications', 0) = 1 THEN
    CALL rotate_notifications(180,(24*3)*60);
    DO RELEASE_LOCK('rotate_notifications');
  END IF;
END ;;

--  DROP EVENT IF EXISTS `update_player_last_seen` ;;
--  CREATE EVENT `update_player_last_seen` ON SCHEDULE EVERY 1 HOUR STARTS '2020-09-14 11:10:05' ON COMPLETION PRESERVE ENABLE DO BEGIN
--    UPDATE `player_last` pl
--    JOIN (SELECT id, memc_get(CONCAT('last_seen:',id)) AS ls FROM `player_last`) t
--      ON t.id = pl.id
--    SET pl.on_pui = FROM_UNIXTIME(t.ls)
--    WHERE t.ls IS NOT NULL;
--  END ;;

DROP EVENT IF EXISTS `update_ranks` ;;
CREATE EVENT `update_ranks` ON SCHEDULE EVERY 30 SECOND STARTS '2021-01-11 12:26:44' ON COMPLETION PRESERVE ENABLE DO
BEGIN
  IF GET_LOCK('update_ranks', 0) = 1 THEN
    CALL calculate_ranks();
    CALL calculate_country_rank();
    CALL calculate_team_ranks();
    DO RELEASE_LOCK('update_ranks');
  END IF;
END ;;

DROP EVENT IF EXISTS `ev_ws_token_expiration` ;;
CREATE EVENT `ev_ws_token_expiration` ON SCHEDULE EVERY 10 MINUTE ON COMPLETION PRESERVE ENABLE DO
BEGIN
  DELETE FROM ws_token WHERE is_server=0 and expires_at<=NOW();
END ;;

DROP EVENT IF EXISTS `ev_scan_failed_claims` ;;
CREATE EVENT `ev_scan_failed_claims` ON SCHEDULE EVERY 1 MINUTE ON COMPLETION PRESERVE ENABLE DO
BEGIN
  IF GET_LOCK('ev_scan_failed_claims', 0) = 1 THEN
    CALL scan_failed_claims();
    DO RELEASE_LOCK('ev_scan_failed_claims');
  END IF;
END ;;

DELIMITER ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
