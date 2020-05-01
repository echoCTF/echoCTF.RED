<?php
namespace app\widgets\stream;

use yii\web\AssetBundle;

class StreamWidgetAsset extends AssetBundle
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
        //$this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}
