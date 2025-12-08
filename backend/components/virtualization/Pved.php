<?php

namespace app\components\virtualization;

use Yii;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\UserException;

/**
 * Component that handles Proxmox Virtualization Enviroment operations
 */
class Pved extends Component
{

  public function init()
  {
    parent::init();
  }

  /**
   * Parse image params into HTTP header values
   */
  public static function getHeaders($imageparams)
  {
    $HEADERS = ['Accept: application/json', 'Content-Type: application/json'];
    if ($imageparams !== '') {
      $decoded = json_decode($imageparams);
      foreach ($decoded as $key => $val) {
        $HEADERS[] = $key . ': ' . $val;
      }
    }
    return $HEADERS;
  }

  /**
   * Simulate docker container spin
   */
  public function spin($target)
  {
    $ch = self::initApi(self::getHeaders($target->imageparams));

    foreach (['status/stop', 'snapshot/echoCTF/rollback', 'status/start'] as $action) {
      $endpoint = str_replace('//', '/', $target->server . $action);
      curl_setopt($ch, CURLOPT_URL, $target->server . $action);
      if (curl_exec($ch) === false)
        throw new UserException('Failed to execute ' . $endpoint . ': ' . curl_error($ch));
      $ch = null;
      sleep(1); // give it some time for the previous request to complete
    }
    return true;
  }

  /**
   * Destroy the remote Virtual Machine
   */
  public function destroy($target)
  {
    $ch = self::initApi(self::getHeaders($target->imageparams));
    $endpoint = str_replace('//', '/', $target->server . '/status/stop');
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    if (curl_exec($ch) === false)
      throw new UserException('Failed to destroy ' . $target->server . ': ' . curl_error($ch));
    $ch = null;
  }

  /**
   * Initialize the API connection
   */
  public static function initApi($HEADERS)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $HEADERS);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
    return $ch;
  }
}
