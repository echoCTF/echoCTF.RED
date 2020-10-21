DELIMITER ;;
IF (SELECT count(*) FROM information_schema.tables WHERE table_schema = 'echoCTF' AND table_name = 'devnull' LIMIT 1)>0 THEN
  CALL echoCTF.init_mysql();
END IF;;
DELIMITER ;
