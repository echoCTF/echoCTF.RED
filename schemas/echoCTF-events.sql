DELIMITER //
DROP EVENT IF EXISTS `update_player_ranks` //
CREATE EVENT `update_player_ranks` ON SCHEDULE EVERY 10 MINUTE ON COMPLETION PRESERVE ENABLE DO
BEGIN
 call calculate_ranks();
END //

DROP EVENT IF EXISTS `update_player_last_seen` //
CREATE EVENT `update_player_last_seen` ON SCHEDULE EVERY 1 HOUR ON COMPLETION PRESERVE ENABLE DO
BEGIN
 UPDATE `player_last` SET `on_pui`=memc_get(CONCAT('last_seen:',id)) WHERE memc_get(CONCAT('last_seen:',id)) IS NOT NULL;
END //
