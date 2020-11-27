<?php
namespace app\widgets\vote;

use yii\web\AssetBundle;

class VoteWidgetAsset extends AssetBundle
{
    public $js=[
        'js/plugins/bootstrap-selectpicker.min.js',
    ];

    public $css=[
        'css/bootstrap-select.min.css',
    ];
//    $this->registerJs('$.fn.selectpicker.Constructor.BootstrapVersion = "4";');

    public $depends=[
        'app\assets\MaterialAsset',
    ];

    public function init()
    {
        // Tell AssetBundle where the assets files are
  //      $this->sourcePath=__DIR__."/assets";
        parent::init();
    }
}
