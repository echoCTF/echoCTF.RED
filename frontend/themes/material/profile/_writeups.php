<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<h3><code><?=count($profile->writeups)?></code> Writeups</h3>
<div class="row">
<?php foreach($profile->writeups as $writeup):?>
<div class="col col-sm-12 col-md-6 col-lg-6 col-xl-3">
  <div class="iconic-card bg-dark">
    <img align="right" class="img-fluid" src="<?=$writeup->target->thumbnail?>"/>
    <p><?php if($writeup->headshot && $writeup->headshot->first):?><img title="1st headshot on the target" alt="1st headshot on the target" align="left" src="/images/1sthelmet.svg" class="img-fluid" style="max-width: 30px"/><?php endif;?><b><?=Html::a(
                 $writeup->target->name,
                  Url::to(['/target/default/versus', 'id'=>$writeup->target_id, 'profile_id'=>$profile->id]),
                  [
                    'style'=>'float: bottom;',
                    'title' => 'View target vs player card',
                    'aria-label'=>'View target vs player card',
                    'data-pjax' => '0',
                  ]
              );?></b></p>
    <p><b><i class="far fa-calendar-alt text-warning"></i> <?=\Yii::$app->formatter->asDate($writeup->created_at,'long')?></b><br/>
    <b><i class="fas fa-book text-secondary"></i> Writeup submitted<?=$writeup->approved? '': ' ('.$writeup->status.')'?></b><br/>
    <?php if($writeup->headshot):?><i class="fas fa-stopwatch text-danger"></i> <?=\Yii::$app->formatter->asDuration($writeup->headshot->timer)?><?php endif;?>
    </p>
  </div>
</div>
<?php endforeach;?>
</div>
