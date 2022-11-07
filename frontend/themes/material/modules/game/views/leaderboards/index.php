<?php
use yii\widgets\Pjax;
use app\widgets\Card;
use app\widgets\leaderboard\Leaderboard;
use yii\widgets\ListView;
use yii\helpers\Html;
$this->_fluid="-fluid";

$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Leaderboards');
$this->_description=$this->title;
$this->_url=\yii\helpers\Url::to(['index'], 'https');

?>
<div class="scoreboard-index">
  <div class="body-content">
    <h3><?=\Yii::t('app','Platform <code>most</code> rankings')?></h3>
    <div class="row">
        <div class="col">
              <?php
              Pjax::begin(['id'=>'playerScore','enablePushState'=>false, 'linkSelector'=>'#player-leaderboard-pager a', 'formSelector'=>false]);
              echo ListView::widget([
                  'id'=>'playerScore',
                  'dataProvider' => $playerDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">'.\Yii::t('app','No player ranks exist at the moment...').'</b></div>',
                  'pager'=>[
                    'options'=>['id'=>'player-leaderboard-pager'],
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
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">'.\Yii::t('app','Player points').'</h4><p class="card-category">'.\Yii::t('app','Individual player scores').'</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_score',
                  'viewParams'=>[
                    'totalPoints'=>$totalPoints,
                  ]
              ]);
              Pjax::end();?>
        </div>
        <div class="col">
              <?php
              Pjax::begin(['id'=>'teamScore','enablePushState'=>false, 'linkSelector'=>'#team-leaderboard-pager a', 'formSelector'=>false]);
              echo ListView::widget([
                  'id'=>'teamScore',
                  'dataProvider' => $teamDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">No team ranks exist at the moment...</b></div>',
                  'pager'=>[
                    'options'=>['id'=>'team-leaderboard-pager'],
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
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">'.\Yii::t('app','Team points').'</h4><p class="card-category">'.\Yii::t('app','Team scores').'</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_team_score',
                  'viewParams'=>[
                    'totalPoints'=>$totalPoints,
                  ]
              ]);Pjax::end();?>
        </div>
      </div>
      <div class="row">
        <div class="col">
              <?php
              Pjax::begin(['id'=>'mostHeadshots','enablePushState'=>false, 'linkSelector'=>'#mostHeadshots-leaderboard-pager a', 'formSelector'=>false]);

              echo ListView::widget([
                  'id'=>'mostHeadshots',
                  'dataProvider' => $mostHeadshotsDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">'.\Yii::t('app','No headshots exist at the moment...').'</b></div>',
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">'.\Yii::t('app','Most headshots').'</h4><p class="card-category">'.\Yii::t('app','Players with most headshots').'</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'pager'=>[
                    'options'=>['id'=>'mostHeadshots-leaderboard-pager'],
                    'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
                    'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
                    'maxButtonCount'=>3,
                    'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link'],
                    'disableCurrentPageButton'=>true,
                    'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
                    'class'=>'yii\bootstrap4\LinkPager',
                  ],

                  'itemView' => '_most_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);Pjax::end();?>
        </div>
        <div class="col">
              <?php
              Pjax::begin(['id'=>'mostSolves','enablePushState'=>false, 'linkSelector'=>'#mostSolves-leaderboard-pager a', 'formSelector'=>false]);
              echo ListView::widget([
                  'id'=>'mostSolves',
                  'emptyText'=>'<div class="card-body"><b class="text-info">'.\Yii::t('app','No challenge solves exist at the moment...').'</b></div>',
                  'dataProvider' => $mostSolvesDataProvider,
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">'.\Yii::t('app','Most challenges solved').'</h4><p class="card-category">'.\Yii::t('app','Players with most challenges solved').'</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'pager'=>[
                    'options'=>['id'=>'mostSolves-leaderboard-pager'],
                    'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
                    'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
                    'maxButtonCount'=>3,
                    'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link'],
                    'disableCurrentPageButton'=>true,
                    'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
                    'class'=>'yii\bootstrap4\LinkPager',
                  ],

                  'itemView' => '_most_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);Pjax::end();?>
        </div>
      </div>
      <h3><?=\Yii::t('app','Platform <code>fastest</code> ranking')?></h3>
      <div class="row">
        <div class="col">
              <?php
              Pjax::begin(['id'=>'headshotTimers','enablePushState'=>false, 'linkSelector'=>'#headshotTimer-leaderboard-pager a', 'formSelector'=>false]);
              echo ListView::widget([
                  'id'=>'headshotTimers',
                  'dataProvider' => $headshotDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">'.\Yii::t('app','No headshots exist at the moment...').'</b></div>',
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-warning"><h4 class="card-title">'.\Yii::t('app','Fastest headshots').'</h4><p class="card-category">'.\Yii::t('app','Players with fastest headshots in seconds').'</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'pager'=>[
                    'options'=>['id'=>'headshotTimer-leaderboard-pager'],
                    'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
                    'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
                    'maxButtonCount'=>3,
                    'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link'],
                    'disableCurrentPageButton'=>true,
                    'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
                    'class'=>'yii\bootstrap4\LinkPager',
                  ],

                  'itemView' => '_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);Pjax::end();?>
        </div>
        <div class="col">
              <?php
              Pjax::begin(['id'=>'fastestSolvers','enablePushState'=>false, 'linkSelector'=>'#solvers-leaderboard-pager a', 'formSelector'=>false]);
              echo ListView::widget([
                  'id'=>'fastestSolvers',
                  'dataProvider' => $solversDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">'.\Yii::t('app','No challenge solves exist at the moment...').'</b></div>',
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-warning"><h4 class="card-title">'.\Yii::t('app','Fastest solves').'</h4><p class="card-category">'.\Yii::t('app','Players with the fastest challenge solves in seconds').'</p></div>',
                  'pager'=>[
                    'options'=>['id'=>'solvers-leaderboard-pager'],
                    'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
                    'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
                    'maxButtonCount'=>3,
                    'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link'],
                    'disableCurrentPageButton'=>true,
                    'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
                    'class'=>'yii\bootstrap4\LinkPager',
                  ],
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_fastest_solve',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);Pjax::end();?>
        </div>

      </div>
      <h3><?=\Yii::t('app','Platform <code>averages</code> rankings')?></h3>
      <div class="row">


        <div class="col">
              <?php
              Pjax::begin(['id'=>'AvgHeadshotTimers','enablePushState'=>false, 'linkSelector'=>'#avgheadshotTimer-leaderboard-pager a', 'formSelector'=>false]);
              echo ListView::widget([
                  'id'=>'AvgHeadshotTimers',
                  'dataProvider' => $AvgHeadshotDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">'.\Yii::t('app','No headshots exist at the moment...').'</b></div>',
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-info"><h4 class="card-title">'.\Yii::t('app','Best average headshots times').'</h4><p class="card-category">'.\Yii::t('app','Players with best average headshots in seconds').'</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'pager'=>[
                    'options'=>['id'=>'avgheadshotTimer-leaderboard-pager'],
                    'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
                    'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
                    'maxButtonCount'=>3,
                    'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link'],
                    'disableCurrentPageButton'=>true,
                    'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
                    'class'=>'yii\bootstrap4\LinkPager',
                  ],
                  'itemView' => '_most_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);Pjax::end();?>
        </div>

        <div class="col-md-6">
              <?php
              Pjax::begin(['id'=>'AvgSolvesTimers','enablePushState'=>false, 'linkSelector'=>'#avgsolvesTimer-leaderboard-pager a', 'formSelector'=>false]);
              echo ListView::widget([
                  'id'=>'AvgSolvesTimers',
                  'dataProvider' => $AvgSolvesDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">'.\Yii::t('app','No challenge solves exist at the moment...').'</b></div>',
                  'options'=>['class'=>'card'],
                  'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
                  'summary'=>'<div class="card-header card-header-info"><h4 class="card-title">'.\Yii::t('app','Best average solve times').'</h4><p class="card-category">'.\Yii::t('app','Players with best average timer solves in seconds').'</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'pager'=>[
                    'options'=>['id'=>'avgsolvesTimer-leaderboard-pager'],
                    'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
                    'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
                    'maxButtonCount'=>3,
                    'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link'],
                    'disableCurrentPageButton'=>true,
                    'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
                    'class'=>'yii\bootstrap4\LinkPager',
                  ],
                  'itemView' => '_most_headshot',
                  'viewParams'=>[
                    'totalPoints'=>0,
                  ]
              ]);Pjax::end();?>
        </div>
      </div>
      <div class="row">
      <div class="col-md-6">
              <?php
              Pjax::begin(['id'=>'playerCountry','enablePushState'=>false, 'linkSelector'=>'#player-country-leaderboard-pager a', 'formSelector'=>false]);
              echo ListView::widget([
                  'id'=>'playerCountry',
                  'dataProvider' => $playerCountryDataProvider,
                  'emptyText'=>'<div class="card-body"><b class="text-info">'.\Yii::t('app','No country ranks exist at the moment...').'</b></div>',
                  'pager'=>[
                    'options'=>['id'=>'player-country-leaderboard-pager'],
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
                  'summary'=>'<div class="card-header card-header-danger"><h4 class="card-title">'.\Yii::t('app','Countries by Players').'</h4><p class="card-category">'.\Yii::t('app','Top countries by players').'</p></div>',
                  'itemOptions' => [
                    'tag' => false
                  ],
                  'itemView' => '_country',
                  'viewParams'=>[
                    'dataProvider'=>$playerCountryDataProvider,
                    'totalPoints'=>$totalPoints,
                  ]
              ]);
              Pjax::end();?>
        </div>
      </div>
    </div>
</div>
