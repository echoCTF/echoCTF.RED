<?php
namespace app\widgets\leaderboard;

use yii\web\AssetBundle;

class LeaderboardAsset extends AssetBundle
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
