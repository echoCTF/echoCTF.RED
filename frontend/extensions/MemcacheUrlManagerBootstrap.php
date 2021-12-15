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
    public function init()
    {
      parent::init();
    }
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
    }
}
