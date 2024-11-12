<?php
/**
 * mysqldump --no-data --no-create-info --skip-triggers --no-create-db --events echoCTF | php clean_events_sql.php "php://stdin"
 */
$rmline[]="/*!50003 SET @saved_time_zone      = @@time_zone */ ;;\n";
$rmline[]="/*!50003 SET time_zone             = 'SYSTEM' */ ;;\n";
$rmline[]="/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;\n";
$rmline[]="/*!50003 SET time_zone             = @saved_time_zone */ ;;\n";
$rmline[]="/*!50003 SET sql_mode              = @saved_sql_mode */ ;;\n";
$rmline[]="/*!50003 SET character_set_client  = @saved_cs_client */ ;;\n";
$rmline[]="/*!50003 SET character_set_results = @saved_cs_results */ ;;\n";
$rmline[]="/*!50003 SET collation_connection  = @saved_col_connection */ ;;\n";
$rmline[]="/*!50003 SET character_set_client  = utf8mb4 */ ;;\n";
$rmline[]="/*!50003 SET character_set_results = utf8mb4 */ ;;\n";
$rmline[]="/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;;\n";
$rmline[]="/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;\n";
$rmline[]="/*!50003 SET sql_mode              = @saved_sql_mode */ ;\n";
$rmline[]="/*!50003 SET character_set_client  = @saved_cs_client */ ;\n";
$rmline[]="/*!50003 SET character_set_results = @saved_cs_results */ ;\n";
$rmline[]="/*!50003 SET collation_connection  = @saved_col_connection */ ;\n";
$rmline[]="/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;\n";
$rmline[]="/*!50003 SET @saved_cs_client      = @@character_set_client */ ;\n";
$rmline[]="/*!50003 SET @saved_cs_results     = @@character_set_results */ ;\n";
$rmline[]="/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;\n";
$rmline[]="/*!50003 SET @saved_col_connection = @@collation_connection */ ;\n";
$rmline[]="/*!50003 SET @saved_col_connection = @@collation_connection */ ;;\n";
$rmline[]="/*!50003 SET character_set_client  = utf8mb4 */ ;\n";
$rmline[]="/*!50003 SET character_set_results = utf8mb4 */ ;\n";
$rmline[]="/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;\n";
$rmline[]="/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;\n";
$rmline[]="/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;\n";
$rmline[]="/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;;\n";
$rmline[]="/*!50003 SET character_set_client  = utf8mb3 */ ;\n";
$rmline[]="/*!50003 SET character_set_results = utf8mb3 */ ;\n";
$rmline[]="/*!50003 SET collation_connection  = utf8mb3_general_ci */ ;\n";
$rmline[]="/*!50003 SET sql_mode              = '' */ ;\n";
$rmline[]="ALTER DATABASE `echoCTF` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;\n";
$rmline[]="ALTER DATABASE `echoCTF` CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci ;\n";
$rmline[]="ALTER DATABASE `echoCTF` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;;\n";
$rmline[]="ALTER DATABASE `echoCTF` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;;\n";
$rmline[]="ALTER DATABASE `echoCTF` CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci ;;\n";
$rmline[]="/*!50017 DEFINER=`moderatorUI`@`10.7.0.201`*/";
$rmline[]="/*!50017 DEFINER=`root`@`localhost`*/";
$rmline[]="/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;\n";
$rmline[]="DELIMITER ;\n";
$rmline[]="DELIMITER ;;\n";
$rmline[]="/*!50003 SET collation_connection  = utf8mb3_unicode_ci */ ;;\n";
$rmline[]=" DEFINER=`root`@`localhost`";
$rmline[]=" DEFINER=`moderatorUI`@`10.7.0.201`";
$rmline[]="/*!50117*/ ";
$rmline[]="/*!50003 SET time_zone             = 'UTC' */ ;;\n";
$rmline[]="/*!50003 SET character_set_client  = utf8mb3 */ ;;\n";
$rmline[]="/*!50003 SET character_set_results = utf8mb3 */ ;;\n";
$rmline[]="/*!50003 SET sql_mode              = '' */ ;;\n";
$rmline[]="/*!50003 SET time_zone             = '+00:00' */ ;;\n";
$input=explode("\n",file_get_contents($argv[1]));
array_splice($input, 1, 5);
array_pop($input);
//array_pop($input);
//
$input=implode("\n",$input);
$input=str_replace($rmline, "", $input);

$rpline=[
  "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;" => "/*!40101 SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci */;",
  '/*!50003 CREATE*/'=>'CREATE',
  '/*!50106 CREATE*/'=>'CREATE',
  '/*!50003 TRIGGER'=>'TRIGGER',
  '/*!50106 EVENT '=>'EVENT',
  'END */;;' => "END ;;\n",
  'END */ ;;'=>"END ;;",
  'END ;;' => "END ;;\n",
  "CHARSET utf8mb3 COLLATE utf8mb3_unicode_ci" => "CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci",
  "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;"=>"/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\nDELIMITER ;;"
];
foreach($rpline as $key => $val)
{
    $input=str_replace($key, $val, $input);
}

$regline=['/\/\*!50106 DROP EVENT IF EXISTS `(\w+)` \*\/;/' => 'DROP EVENT IF EXISTS `$1` ;'];
foreach($regline as $key => $val)
{
    $input=preg_replace($key,$val,$input);
}

echo $input;

