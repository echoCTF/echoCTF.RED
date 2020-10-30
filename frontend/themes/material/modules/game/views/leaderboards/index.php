<?php
use yii\widgets\Pjax;
use app\widgets\Card;
use app\widgets\leaderboard\Leaderboard;
use yii\widgets\ListView;
use yii\helpers\Html;
$this->_fluid="-fluid";

$this->title=Yii::$app->sys->event_name.' Leaderboards' ;
$this->_description=$this->title;

?>
<div class="scoreboard-index">
  <div class="body-content">
    <h3>Platform Leaderboards</h3>
    <div class="row">
        <div class="col-md-4">
              <?php
              echo ListView::widget([
                  'id'=>'playerScore',
                  'dataProvider' => $playerDataProvider,
                  'options'=>['id'=>'player-leaderboard-pager'],
                  'pager'=>[
                    'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
                    'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
                    'maxButtonCount'=>3,
                    'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link'],
                    'disableCurrentPageButton'=>true,
                    'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
                    'class'=>'yii\bootstrap4\LinkPager',
                  ],
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">Most points</h4><p class="card-category">Individual player scores</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_score',
                  'viewParams'=>[
                    'totalPoints'=>$totalPoints,
                  ]
              ]);?>
        </div>

        <div class="col-md-4">
              <?php
              echo ListView::widget([
                  'id'=>'mostHeadshots',
                  'dataProvider' => $mostHeadshotsDataProvider,
                  'options'=>['id'=>'mostHeadshots-leaderboard-pager'],
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-primary"><h4 class="card-title">Most headshots</h4><p class="card-category">Players with most headshots</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_most_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);?>
        </div>
        <div class="col-md-4">
              <?php
              echo ListView::widget([
                  'id'=>'headshotTimers',
                  'dataProvider' => $headshotDataProvider,
                  'options'=>['id'=>'headshotTimer-leaderboard-pager'],
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-warning"><h4 class="card-title">Fastest headshots</h4><p class="card-category">Players with fastest headshots in seconds</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);?>
        </div>

      </div>
    </div>
</div>
