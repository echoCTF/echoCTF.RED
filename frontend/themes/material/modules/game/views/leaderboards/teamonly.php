<?php

use yii\widgets\Pjax;
use app\widgets\Card;
use app\widgets\leaderboard\Leaderboard;
use yii\widgets\ListView;
use yii\helpers\Html;

$this->_fluid = "-fluid";

$this->title = Yii::$app->sys->event_name . ' ' . \Yii::t('app', 'Leaderboard');
$this->_description = $this->title;
$this->_url = \yii\helpers\Url::to(['index'], 'https');

?>
<div class="scoreboard-index">
  <div class="body-content">
    <center><h3><?= \Yii::t('app', 'Leaderboard') ?></h3></center>
    <div class="row d-flex justify-content-center">
        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-8">
          <?php
          Pjax::begin(['id' => 'teamScore-pjax', 'enablePushState' => false, 'linkSelector' => '#team-leaderboard-pager a', 'formSelector' => false]);
          echo ListView::widget([
            'id' => 'teamScore',
            'dataProvider' => $teamDataProvider,
            'emptyText' => '<div class="card-body"><b class="text-info">No team ranks exist at the moment...</b></div>',
            'pager' => [
              'options' => ['id' => 'team-leaderboard-pager'],
              'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
              'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
              'maxButtonCount' => 3,
              'linkOptions' => ['class' => ['page-link'], 'aria-label' => 'Pager link'],
              'disableCurrentPageButton' => true,
              'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
              'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
              'class' => 'yii\bootstrap4\LinkPager',
            ],
            'options' => ['class' => 'card'],
            'layout' => '{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
            'summary' => '<div class="card-header card-header-score"><h4 class="card-title">' . \Yii::t('app', 'Team points') . '</h4><p class="card-category">' . \Yii::t('app', 'Team scores') . '</p></div>',
            'itemOptions' => [
              'tag' => false
            ],
            'itemView' => '_team_score',
            'viewParams' => [
              'totalPoints' => $totalPoints,
            ]
          ]);
          Pjax::end(); ?>
        </div>
    </div>
  </div>
</div>