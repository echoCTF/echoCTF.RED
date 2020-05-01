<?php
namespace app\widgets\target;

use yii\web\AssetBundle;

class TargetWidgetAsset extends AssetBundle
{
    public $js=[
    ];

    public $css=[
    ];

    public $depends=[
    ];

    public function init()
    {
        // Tell AssetBundle where the assets files are
//        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}
