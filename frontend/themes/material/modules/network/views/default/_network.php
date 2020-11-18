<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\widgets\Twitter;
use app\widgets\Card;

?>
<div class="col col-lg-4 col-md-6 col-sm-6">
  <div class="card card-profile">
    <div class="card-icon bg-primary">
      <img class="img" src="<?=$model->icon?>" height="50px"/>
    </div>
    <div class="card-body table-responsive">
      <h4><?=Html::a($model->name . ($model->public ? ' (public)' : ''),
                  Url::to(['/network/default/view', 'id'=>$model->id]),
                  [
                    'style'=>'float: bottom;',
                    'title' => 'View Network details',
                    'aria-label'=>'View network details',
                    'data-pjax' => '0',
                  ]
              );?></h4>
      <p style="text-align: justify;"><?=$model->description?></p>
      <h6 class="badge badge-primary"><?=$model->targetsCount?> targets</h6>
  </div>
 </div>
</div>
