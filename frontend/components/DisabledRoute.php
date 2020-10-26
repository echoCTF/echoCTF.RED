<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "disabled_routes".
 *
 * @property string $route
 */
class DisabledRoute extends Component
{

  static function disabled($action)
  {
    if(property_exists($action->controller->module,'requestedRoute'))
      $route=$action->controller->module->requestedRoute;
    elseif(property_exists($action->controller->module,'module') && property_exists($action->controller->module->module,'requestedRoute'))
      $route=$action->controller->module->module->requestedRoute;
    else
      $route=$action->controller->id.'/'.$action->id;

    if((int)\Yii::$app->db->createCommand("SELECT count(*) FROM disabled_route WHERE :route LIKE route")->bindValue(':route', $route)->queryScalar()>0)
      return true;

    return false;
  }

}
