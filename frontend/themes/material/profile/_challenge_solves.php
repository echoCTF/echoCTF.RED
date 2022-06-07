<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<h3><code><?=count($profile->owner->challengeSolvers)?></code> Challenges solved</h2>
<div class="row">
  <?php foreach($profile->owner->challengeSolvers as $cs):?>
    <div class="col col-sm-6 col-md-6 col-lg-6 col-xl-3">
      <div class="iconic-card bg-dark">
            <?=$cs->challenge->icon?>
            <p><?php if($cs->first):?><img title="1st solver of the challenge" alt="1st solver of the challenge" align="left" src="/images/1sthelmet.svg" class="img-fluid" style="max-width: 30px"/><?php endif;?><b><?=Html::a(
                    $cs->challenge->name,
                      Url::to(['/challenge/default/view', 'id'=>$cs->challenge_id]),
                      [
                        'style'=>'float: bottom;',
                        'title' => 'View challenge',
                        'aria-label'=>'View challenge',
                        'data-pjax' => '0',
                      ]
                  );?></b></p>
                    <p><b><i class="fas fa-list-ul text-info"></i> <?=count($cs->challenge->questions)?> Questions / <?=$cs->challenge->difficulty?></b><br /><b><i class="far fa-calendar-alt text-warning"></i> <?=\Yii::$app->formatter->asDate($cs->created_at,'long')?></b><br/>
                    <?php if($cs->timer>0):?><i class="fas fa-stopwatch text-danger"></i> <?=\Yii::$app->formatter->asDuration($cs->timer)?><?php endif;?></p>
      </div>
    </div>
  <?php endforeach;?>
</div>
