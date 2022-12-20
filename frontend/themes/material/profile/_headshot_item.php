<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="col col-sm-12 col-md-6 col-lg-6 col-xl-3">
  <div class="iconic-card bg-dark">
    <img align="right" class="img-fluid" src="<?=$model->target->thumbnail?>"/>
    <p><?php if($model->first):?><img title="1st headshot on the target" alt="1st headshot on the target" align="left" src="/images/1sthelmet.svg" class="img-fluid" style="max-width: 30px"/><?php endif;?><b><?=Html::a(
                 $model->target->name,
                  Url::to(['/target/default/versus', 'id'=>$model->target_id, 'profile_id'=>$profile->id]),
                  [
                    'style'=>'float: bottom;',
                    'title' => 'View target vs player card',
                    'aria-label'=>'View target vs player card',
                    'data-pjax' => '0',
                  ]
              );?></b></p>
    <p><b><i class="far fa-calendar-alt text-warning"></i> <?=\Yii::$app->formatter->asDate($model->created_at,'long')?></b><br/>
    <?php if($model->timer>0):?><i class="fas fa-stopwatch text-danger"></i> <?=\Yii::$app->formatter->asDuration($model->timer)?><?php endif;?>
    </p>
  </div>
</div>
