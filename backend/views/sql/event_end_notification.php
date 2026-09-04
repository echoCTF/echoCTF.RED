<?php
/**
 * @var string $val Timestamp value to insert into the SQL
 */
?>
CREATE EVENT event_end_notification ON SCHEDULE AT '<?= $val ?>' DO BEGIN
  DECLARE apply_abuse_penalties INT DEFAULT 0;

  SET apply_abuse_penalties = memc_get('sysconfig:apply_abuse_penalties');

  INSERT INTO `notification`(player_id,category,title,body,archived)
  SELECT id,'swal:info',
         memc_get('sysconfig:event_end_notification_title'),
         memc_get('sysconfig:event_end_notification_body'),
         0
  FROM player WHERE status=10;

  DO memc_set('event_finished',1);

  SET @event_path = COALESCE(memc_get('sysconfig:event_finished_path'), '/watch/event_finished');
  SET @sql_cmd  = CONCAT("SELECT sleep(1) INTO OUTFILE ", QUOTE(@event_path));
  EXECUTE IMMEDIATE @sql_cmd;
END
