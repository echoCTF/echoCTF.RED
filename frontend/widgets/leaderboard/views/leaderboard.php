<?php
use yii\widgets\ListView;

echo ListView::widget([
    'id'=>$divID,
    'dataProvider' => $dataProvider,
    'emptyText'=>'<div class="card-body"><b class="text-info">'.\Yii::t('app','No ranks exist at the moment...').'</b></div>',
    'pager'=>[
      'options'=>['id'=>$pagerID],
      'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
      'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
      'maxButtonCount'=>3,
      'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link','rel'=>'nofollow'],
      'disableCurrentPageButton'=>true,
      'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
      'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
      'class'=>'yii\bootstrap4\LinkPager',
    ],
    'options'=>['class'=>'card'],
    'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
    'summary'=>$summary,
    'itemOptions' => [
      'tag' => false
    ],
    'itemView' => '_score',
    'viewParams'=>[
      'totalPoints'=>$totalPoints,
      'player_id'=>$player_id,
    ]
]);
