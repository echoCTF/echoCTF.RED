<?php
/**
 * Vote widget
 * sample usage:
 *
 *  use app\components\widgets\vote\VoteWidget;
 *  $fakedModel = (object)['title'=> 'A Product', ];
 *  echo VoteWidget::widget(['model' => $fakedModel]);
 */

namespace app\widgets\leaderboard;

use yii\base\Widget;
use yii\widgets\ListView;
use yii\helpers\Html;
class Leaderboard extends Widget
{
    public $divID='Leaderboard';
    public $dataProvider;
    public $totalPoints;
    public $pagerID='leaderboard-pager';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        LeaderboardAsset::register($this->getView());
        return $this->render('leaderboard',['dataProvider'=>$this->dataProvider,'totalPoints'=>$this->totalPoints,'divID'=>$this->divID,'pagerID'=>$this->pagerID]);
    }
}
