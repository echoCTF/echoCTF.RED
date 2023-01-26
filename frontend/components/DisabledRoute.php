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
   * Get the disabled_routes depending on memcached availability
   * @return string
   */
  public static function disabled_routes()
  {
    if(Yii::$app->sys->disabled_routes!==false && Yii::$app->sys->disabled_routes!==null)
    {
      return Yii::$app->sys->disabled_routes;
    }
    $lrfile=\Yii::getAlias("@app/config/disabled-routes.php");
    if(file_exists($lrfile))
    {
      $dr=include $lrfile;
      return json_encode($dr,JSON_UNESCAPED_SLASHES);
    }
  }

  /**
   * Get the player_disabled_routes depending on memcached availability
   * @return string
   */
  public static function player_disabled_routes()
  {
    if(Yii::$app->sys->player_disabled_routes!==false && Yii::$app->sys->player_disabled_routes!==null)
    {
      return Yii::$app->sys->player_disabled_routes;
    }
    $lrfile=\Yii::getAlias("@app/config/player_disabled-routes.php");
    if(file_exists($lrfile))
    {
      $dr=include $lrfile;
      return json_encode($dr,JSON_UNESCAPED_SLASHES);
    }
  }

  /**
   * Check if current action is disabled
   * @param string|object $action
   * @return bool
   */
  public static function disabled($action):bool
  {

    $disabled_routes=self::disabled_routes();
    $player_disabled_routes=self::player_disabled_routes();

    if(is_object($action))
    {
      $route=self::RequestedRoute($action);
    }
    else
    {
      $route=$action;
    }
    if($disabled_routes!==false && $disabled_routes!='' && self::match($route,$disabled_routes))
    {
      return true;
    }
    if($disabled_routes!==false && $player_disabled_routes!='' && self::match($route,$player_disabled_routes,true))
    {
      return true;
    }

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

  public static function match($against,$disabled_routes,bool $with_player=false):bool
  {
    // make sure we start with /
    if($against[0]!=='/')
      $against="/$against";

    foreach(json_decode($disabled_routes,true) as $r)
    {
      $route=$r['route'];
      if($route[0]!=='/' && $route[0]!=='%')
        $route="/$route";

      if($with_player===true && !Yii::$app->user->isGuest && intval($r['player_id'])!==Yii::$app->user->id)
      {
        /*
         * go back if the record refers to player
         * specific disabled route and we're not currently that player
         */
        continue;
      }

      if($route[0]==='%' && endsWith($against,substr($route, 1))) // must end with this
      {
        return true;
      }
      else if($route[-1]==='%' && startsWith($against,substr($route, 0,-1))) // must start with this
      {
        return true;
      }
      else if($route===$against)
      {
        return true;
      }
    }
    return false;
  }

}
function startsWith( $haystack, $needle ): bool {
  if(function_exists('str_starts_with')){
    return str_starts_with($haystack,$needle);
  }
  $length = strlen( $needle );
  return substr( $haystack, 0, $length ) === $needle;
}
function endsWith( $haystack, $needle ) {
  if(function_exists('str_ends_with')){
    return str_ends_with($haystack,$needle);
  }
  $length = strlen( $needle );
  if( !$length ) {
      return true;
  }
  return substr( $haystack, -$length ) === $needle;
}