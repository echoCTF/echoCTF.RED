<?php
namespace app\extensions;

use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;
/**
 * Class ModuleBootstrap
 *
 * @package app\extensions
 */
class MemcacheUrlManagerBootstrap implements BootstrapInterface
{
  /**
     * @param \yii\base\Application $oApplication
     */
    public function bootstrap($oApplication)
    {
      if($oApplication->sys->routes!==false && $oApplication->sys->routes!=="")
      {
        $mcRoutes=json_decode($oApplication->sys->routes);
        if($mcRoutes!==false && $mcRoutes!==NULL)
        {
          $routes=ArrayHelper::map($mcRoutes, 'source', 'destination');
          $oApplication->getUrlManager()->addRules($routes, false);
        }
      }
      else // for some reason we failed to load the routes from memcached
      {
        $lrfile=\Yii::getAlias("@app/config/routes.php");
        if(file_exists($lrfile))
        {
          $localroutes=include $lrfile;
          $oApplication->getUrlManager()->addRules($localroutes, false);
        }
      }
    }
}
