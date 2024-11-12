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
    ALTER EVENT `ev_player_token_expiration` DISABLE;
      call expire_player_tokens();
    ALTER EVENT `ev_player_token_expiration` ENABLE;
  END ;;

DROP EVENT IF EXISTS `player_maintenance` ;;
CREATE EVENT `player_maintenance` ON SCHEDULE EVERY 1 DAY STARTS '2020-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
    CALL player_maintenance();
  END ;;

DROP EVENT IF EXISTS `rotate_notifications` ;;
CREATE EVENT `rotate_notifications` ON SCHEDULE EVERY 12 HOUR STARTS '2023-04-03 00:00:01' ON COMPLETION PRESERVE ENABLE DO BEGIN
      ALTER EVENT `rotate_notifications` DISABLE;
      CALL rotate_notifications(180,(24*3)*60);
      ALTER EVENT `rotate_notifications` ENABLE;
    END ;;

DROP EVENT IF EXISTS `update_player_last_seen` ;;
CREATE EVENT `update_player_last_seen` ON SCHEDULE EVERY 1 HOUR STARTS '2020-09-14 11:10:05' ON COMPLETION PRESERVE ENABLE DO BEGIN
 UPDATE `player_last` SET `on_pui`=FROM_UNIXTIME(memc_get(CONCAT('last_seen:',id))) WHERE memc_get(CONCAT('last_seen:',id)) IS NOT NULL;
END ;;

DROP EVENT IF EXISTS `update_ranks` ;;
CREATE EVENT `update_ranks` ON SCHEDULE EVERY 30 SECOND STARTS '2021-01-11 12:26:44' ON COMPLETION PRESERVE ENABLE DO BEGIN
    ALTER EVENT `update_ranks` DISABLE;
    call calculate_ranks();
    call calculate_country_rank();
    call calculate_team_ranks();
    ALTER EVENT `update_ranks` ENABLE;
  END ;;

DELIMITER ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
