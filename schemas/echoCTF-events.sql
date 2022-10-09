SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

DELIMITER //

DROP EVENT IF EXISTS `update_player_ranks` //
CREATE EVENT `update_player_ranks` ON SCHEDULE EVERY 1 MINUTE ON COMPLETION PRESERVE ENABLE DO
BEGIN
  call calculate_ranks();
  call calculate_country_rank();
END //

DROP EVENT IF EXISTS `update_player_last_seen` //
CREATE EVENT `update_player_last_seen` ON SCHEDULE EVERY 1 HOUR ON COMPLETION PRESERVE ENABLE DO
BEGIN
 UPDATE `player_last` SET `on_pui`=FROM_UNIXTIME(memc_get(CONCAT('last_seen:',id))) WHERE memc_get(CONCAT('last_seen:',id)) IS NOT NULL;
END //

DROP EVENT IF EXISTS `rotate_notifications` //
CREATE EVENT `rotate_notifications` ON SCHEDULE EVERY 1 DAY ON COMPLETION PRESERVE ENABLE DO
BEGIN
 DELETE FROM `notification` WHERE `archived`=1 AND `updated_at` < NOW() - INTERVAL 7 DAY;
END //

DROP EVENT IF EXISTS `player_maintenance` //
CREATE EVENT `player_maintenance` ON SCHEDULE EVERY 1 DAY STARTS '2020-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO
BEGIN
 DELETE FROM `player` WHERE `created` < NOW() - INTERVAL 10 DAY AND `status` IN (0,9);
 UPDATE `player` SET `password_reset_token`=NULL WHERE `status`=10 AND `password_reset_token` IS NOT NULL AND FROM_UNIXTIME(SUBSTR(`password_reset_token`,-10)) <= NOW() - INTERVAL 1 DAY;
END //

--
-- This event can be used in heavy loaded systems to calculate the timers for the
-- headshots after they have been assigned. This will require modifications to
-- the tai_player_finding and tai_player_treasure.
--
-- DROP EVENT IF EXISTS `update_headshot_timers` //
-- CREATE EVENT `update_headshot_timers` ON SCHEDULE EVERY 10 SECOND ON COMPLETION PRESERVE ENABLE DO
-- BEGIN
--   DECLARE ltarget_id,lplayer_id INT;
--   ALTER EVENT `update_headshot_timers` DISABLE;
--   SELECT target_id,player_id INTO ltarget_id,lplayer_id from headshot where timer=0 order by created_at asc LIMIT 1;
--   CALL time_headshot(lplayer_id,ltarget_id);
--   ALTER EVENT `update_headshot_timers` ENABLE;
-- END //
