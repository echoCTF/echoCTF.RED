<?php
namespace app\widgets\solver;

use yii\web\AssetBundle;

class SolverWidgetAsset extends AssetBundle
{
    public $js=[
      //  'js/votewidget.js'
    ];

    public $css=[
    //    'css/votewidget.css'
    ];

    public $depends=[
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        // Tell AssetBundle where the assets files are
        //$this->sourcePath=__DIR__."/assets";
        parent::init();
    }
}
