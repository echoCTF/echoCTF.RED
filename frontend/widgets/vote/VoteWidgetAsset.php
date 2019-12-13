<?php
namespace app\components\widgets\vote;

use yii\web\AssetBundle;

class VoteWidgetAsset extends AssetBundle
{
    public $js = [
        'js/votewidget.js'
    ];

    public $css = [
        'css/votewidget.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        // Tell AssetBundle where the assets files are
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}
