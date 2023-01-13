<?php

/**
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @copyright 2022
 * @since 0.20.2
 */

namespace app\commands;

use Yii;
use yii\console\Exception as ConsoleException;
use yii\helpers\Console;
use yii\console\Controller;

/**
 * Perform start/stop migration steps
 *
 * @author proditis
 */
class MaintenanceController extends Controller
{

  public function actionStart()
  {
    echo "Stopping event scheduler\n";
    Yii::$app->db->createCommand("SET GLOBAL EVENT_SCHEDULER=OFF")->execute();
    Yii::$app->db->createCommand("FLUSH TABLES")->execute();
    Yii::$app->db->createCommand("FLUSH LOGS")->execute();
    Yii::$app->db->createCommand("PURGE BINARY LOGS BEFORE now()")->execute();
    $disabled_events = Yii::$app->db->createCommand("SELECT EVENT_NAME FROM information_schema.EVENTS WHERE EVENT_SCHEMA='echoCTF' AND status='DISABLED'")->queryAll();
    foreach ($disabled_events as $row) {
      $evname = $row['EVENT_NAME'];
      echo "Found disabled event ", $evname, ", setting to ENABLE\n";
      Yii::$app->db->createCommand("ALTER EVENT $evname ENABLE")->execute();
    }
  }
  public function actionStop()
  {
    echo "Starting event scheduler\n";
    Yii::$app->db->createCommand("SET GLOBAL EVENT_SCHEDULER=ON")->execute();
    $disabled_events = Yii::$app->db->createCommand("SELECT EVENT_NAME FROM information_schema.EVENTS WHERE EVENT_SCHEMA='echoCTF' AND status='DISABLED'")->queryAll();
    foreach ($disabled_events as $row) {
      $evname = $row['EVENT_NAME'];
      echo "Found disabled event ", $evname, ", setting to ENABLE\n";
      Yii::$app->db->createCommand("ALTER EVENT $evname ENABLE")->execute();
    }
    echo "Enable Triggers";
    Yii::$app->db->createCommand("SET @TRIGGER_CHECKS=TRUE")->execute();
  }

  public function actionTruncateAll()
  {
    $this->actionTruncateInstanceAudit();
    $this->actionTruncateSpinHistory();
    $this->actionTruncateVpnHistory();
    $this->actionPurgeOldNotifications();
  }

  public function actionTruncateInstanceAudit()
  {
    Yii::$app->db->createCommand()->truncateTable('target_instance_audit')->execute();
  }

  public function actionTruncateVpnHistory()
  {
    Yii::$app->db->createCommand()->truncateTable('player_vpn_history')->execute();
  }

  public function actionTruncateSpinHistory()
  {
    Yii::$app->db->createCommand()->truncateTable('spin_history')->execute();
  }

  public function actionPurgeOldNotifications()
  {
    Yii::$app->db->createCommand("DELETE FROM `notification` WHERE created_at < NOW()-INTERVAL 40 DAY")->execute();
  }

}
