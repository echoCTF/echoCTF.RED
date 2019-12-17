<?php
use yii\widgets\ListView;

echo ListView::widget([
    'id'=>$divID,
    'dataProvider' => $dataProvider,
    'pager'=>[
      'options'=>['id'=>$pagerID],
      'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
      'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
      'maxButtonCount'=>3,
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
