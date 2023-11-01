<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\widgets\Twitter;
?>
<div class="col-xl-4 col-sm-6 d-flex align-items-stretch">
  <div class="card card-challenge">
    <div class="card-body">
      <?=$model->icon?>
      <h4 class="card-title"><b><?=Html::a($model->name, ['view', 'id'=>$model->id])?>
      <?php if($model->completed):?><i class="fas fa-check-double text-primary"></i> <?=Twitter::widget(['message'=>\Yii::t('app','Hey check this out, I completed the challenge ').Html::encode($model->name), 'url'=>Url::to(['view', 'id'=>$model->id], 'https')]);?><?php else:?><?=Twitter::widget(['message'=>'I currently grinding the challenge '.Html::encode($model->name), 'url'=>Url::to(['view', 'id'=>$model->id], 'https')]);?><?php endif;?></b></h4>
      <p class="card-text"><b><?=\Yii::t('app',"Category:")?></b> <?=Html::encode($model->category);?><br/><b><?=\Yii::t('app',"Difficulty:")?></b> <?=Html::encode($model->difficulty)?></p>
    </div>
    <div class="card-footer"><?=Html::a(\Yii::t('app',"Go to challenge"), ['view', 'id'=>$model->id],['class'=>'btn btn-primary card-link'])?></div>
  </div>
</div>
