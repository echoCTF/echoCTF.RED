<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "disabled_routes".
 *
 * @property string $route
 * @method disabled
 * @method enabled
 * @method disabled
 */
class DisabledRoute extends Component
{

  public function init()
  {
    parent::init();
  }

  /**
   * Check if current action is disabled
   * @param string|object $action
   * @return bool
   */
  public static function disabled($action):bool
  {
    if(is_object($action))
    {
      $route=self::RequestedRoute($action);
    }
    else
    {
      $route=$action;
    }
    if((int)\Yii::$app->db->createCommand("SELECT count(*) FROM disabled_route WHERE :route LIKE route OR CONCAT('/',:route) LIKE route")->bindValue(':route', $route)->queryScalar()>0)
      return true;

    return false;
  }

  /** Check if current action is enabled
   * @param string|object $action
   * @return bool
   */
  public static function enabled($action):bool
  {
    return !self::disabled($action);
  }


  public static function RequestedRoute($action)
  {
    if(property_exists($action->controller->module,'requestedRoute'))
      return $action->controller->module->requestedRoute;
    elseif(property_exists($action->controller->module,'module') && property_exists($action->controller->module->module,'requestedRoute'))
      return $action->controller->module->module->requestedRoute;

    return $action->controller->id.'/'.$action->id;
  }

}
