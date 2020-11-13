<?php
use yii\widgets\Pjax;
use app\widgets\Card;
use app\widgets\leaderboard\Leaderboard;
use yii\widgets\ListView;
use yii\helpers\Html;
$this->_fluid="-fluid";

$this->title=Yii::$app->sys->event_name.' Leaderboards' ;
$this->_description=$this->title;
$this->_url=\yii\helpers\Url::to(['index'], 'https');

?>
<div class="scoreboard-index">
  <div class="body-content">
    <h3>Platform <code>most</code> rankings</h3>
    <div class="row">
        <div class="col">
              <?php
              echo ListView::widget([
                  'id'=>'playerScore',
                  'dataProvider' => $playerDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">No player ranks exist at the moment...</b></div>',
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
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">Player points</h4><p class="card-category">Individual player scores</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_score',
                  'viewParams'=>[
                    'totalPoints'=>$totalPoints,
                  ]
              ]);?>
        </div>
        <div class="col">
              <?php
              echo ListView::widget([
                  'id'=>'teamScore',
                  'dataProvider' => $teamDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">No team ranks exist at the moment...</b></div>',
                  'options'=>['id'=>'team-leaderboard-pager'],
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
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">Team points</h4><p class="card-category">Team scores</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_team_score',
                  'viewParams'=>[
                    'totalPoints'=>$totalPoints,
                  ]
              ]);?>
        </div>
      </div>
      <div class="row">
        <div class="col">
              <?php
              echo ListView::widget([
                  'id'=>'mostHeadshots',
                  'dataProvider' => $mostHeadshotsDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">No headshots exist at the moment...</b></div>',
                  'options'=>['id'=>'mostHeadshots-leaderboard-pager'],
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">Most headshots</h4><p class="card-category">Players with most headshots</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_most_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);?>
        </div>
        <div class="col">
              <?php
              echo ListView::widget([
                  'id'=>'mostSolves',
                  'emptyText'=>'<div class="card-body"><b class="text-info">No challenge solves exist at the moment...</b></div>',
                  'dataProvider' => $mostSolvesDataProvider,
                  'options'=>['id'=>'mostSolves-leaderboard-pager'],
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">Most challenges solved</h4><p class="card-category">Players with most challenges solved</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_most_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);?>
        </div>
      </div>
      <h3>Platform <code>fastest</code> ranking</h3>
      <div class="row">
        <div class="col">
              <?php
              echo ListView::widget([
                  'id'=>'headshotTimers',
                  'dataProvider' => $headshotDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">No headshots exist at the moment...</b></div>',
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
        <div class="col">
              <?php
              echo ListView::widget([
                  'id'=>'fastestSolvers',
                  'dataProvider' => $solversDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">No challenge solves exist at the moment...</b></div>',
                  'options'=>['id'=>'solvers-leaderboard-pager'],
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-warning"><h4 class="card-title">Fastest solves</h4><p class="card-category">Players with the fastest challenge solves in seconds</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_fastest_solve',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);?>
        </div>

      </div>
      <h3>Platform <code>averages</code> rankings</h3>
      <div class="row">


        <div class="col">
              <?php
              echo ListView::widget([
                  'id'=>'AvgHeadshotTimers',
                  'dataProvider' => $AvgHeadshotDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">No headshots exist at the moment...</b></div>',
                  'options'=>['id'=>'avgheadshotTimer-leaderboard-pager'],
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-info"><h4 class="card-title">Best average headshots times</h4><p class="card-category">Players with best average headshots in seconds</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_most_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);?>
        </div>

        <div class="col-md-6">
              <?php
              echo ListView::widget([
                  'id'=>'AvgSolvesTimers',
                  'dataProvider' => $AvgSolvesDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">No challenge solves exist at the moment...</b></div>',
                  'options'=>['id'=>'avgsolvesTimer-leaderboard-pager'],
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-info"><h4 class="card-title">Best average solve times</h4><p class="card-category">Players with best average timer solves in seconds</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_most_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);?>
        </div>


      </div>
    </div>
</div>
