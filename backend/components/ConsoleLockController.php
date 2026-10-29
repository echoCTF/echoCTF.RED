<?php

namespace app\components;

use Yii;
use yii\console\Controller;
use yii\base\UserException;

class ConsoleLockController extends Controller
{
  /** @var string|null lock file held by the running action */
  private $_lock;

  /**
   * afterAction() is skipped when the action throws, release the lock we
   * acquired anyway (but never one held by another process).
   */
  public function runAction($id, $params = [])
  {
    try {
      return parent::runAction($id, $params);
    } finally {
      if ($this->_lock !== null) {
        $this::lockClear($this->_lock);
        $this->_lock = null;
      }
    }
  }

  private static function lockCheck($lock)
  {
    if (file_exists($lock)) {
      throw new UserException(\Yii::t('app', '{date} {action} {lock} exists since {since}, skipping execution.', [
        'date' => date("Y-m-d H:i:s"),
        'action' => \Yii::$app->controller->action->id,
        'lock' => $lock,
        'since' => Yii::$app->formatter->asRelativeTime(stat($lock)[10])
      ]));
    }
  }
  private static function lockGet($lock)
  {
    touch($lock);
  }

  private static function lockClear($lock)
  {
    @unlink($lock);
  }

  public function beforeAction($action)
  {
    if (\Yii::$app->controller->action->id != 'index') {
      $lock = sprintf('/tmp/%s-%s.lock', \Yii::$app->controller->id, $action->id);
      // Check for lock existance
      $this::lockCheck($lock);
      // Request a lock
      $this::lockGet($lock);
      $this->_lock = $lock;
    }

    return parent::beforeAction($action);
  }

  public function afterAction($action, $result)
  {
    if (\Yii::$app->controller->action->id != 'index') {
      $lock = sprintf('/tmp/%s-%s.lock', \Yii::$app->controller->id, $action->id);
      // Clear the lock
      $this::lockClear($lock);
      $this->_lock = null;
    }

    return parent::afterAction($action, $result);
  }
}
