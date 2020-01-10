<?php
use app\widgets\Card;
use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title"><?=$this->title?></h4>
    <p class="card-category"><?=$target->purpose?></p>
  </div>
  <div class="card-body table-responsive">
<?=DetailView::widget([
  'id'=>'target-fulldetails',
  'model' => $target,
  'options'=>['class'=>'table table-striped table-condenced detail-view'],
  'attributes' => [
    'fqdn',
    [
      'attribute'=>'ip',
      'value'=>long2ip($target->ip)
    ],
    [
      'attribute'=>'difficulty',
      'value'=>$target->difficultyText,
    ],
    'rootable:boolean',
    [
      'label'=>'Total points',
      'type'=>'number',
      'value'=>number_format($target->points),
    ],
    [
      'label'=>'Flags / Services',
      'format'=>'raw',
      'value'=>'<i class="fas fa-flag"></i> '.count($target->treasures).' / <i class="fas fa-fingerprint"></i> '.count($target->findings) ,
    ],

    [
      'label'=>'Headshots',
      'format'=>'raw',
      'value'=>function($model){
                $headshots=null;
                foreach($model->headshots as $hs)
                  if((int)$hs->player->active===1)
                    $headshots[]=$hs->player->profile->link;
              if ($headshots===NULL) return "None";
              return implode(", ",$headshots);
            }
    ],
    [
      'attribute'=>'description',
      'label'=>false,
      'format'=>'html',
    ],
  ],
]) ?>
<?php /* echo ListView::widget([
    'id'=>'target-headshots',
    'dataProvider' => $headshotsProvider,
    'options'=>['class'=>"Leaderboard col-md-3","style"=>"padding-top: 0em; margin-top: -4em"],
    'summary'=>'<h3>'.$target->countHeadshots.' Headshots</h3>',
    'itemOptions' => [
      'tag' => false
    ],
    'itemView' => '_headshot',
    'viewParams'=>['totalPoints'=>$target->points]
]);*/?>
</div>
</div>
