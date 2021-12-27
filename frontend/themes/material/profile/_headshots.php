<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<h3><code><?=$profile->headshotsCount?></code> Headshots / <small>Average time: <?php
$hs=\app\modules\game\models\Headshot::find()->timed()->player_avg_time($profile->player_id)->one();
if($hs && $hs->average > 0)
  echo number_format($hs->average / 60), " minutes";
?> <sub>(ordered by date)</small></sub></h3>
<div class="row">
<?php foreach($profile->owner->headshots as $headshot):?>
<div class="col col-sm-12 col-md-6 col-lg-6 col-xl-3">
  <div class="iconic-card bg-dark">
    <img align="right" class="img-fluid" src="<?=$headshot->target->thumbnail?>"/>
    <p><?php if($headshot->first):?><img title="1st headshot on the target" alt="1st headshot on the target" align="left" src="/images/1sthelmet.svg" class="img-fluid" style="max-width: 30px"/><?php endif;?><b><?=Html::a(
                 $headshot->target->name.' / '.long2ip($headshot->target->ip),
                  Url::to(['/target/default/versus', 'id'=>$headshot->target_id, 'profile_id'=>$profile->id]),
                  [
                    'style'=>'float: bottom;',
                    'title' => 'View target vs player card',
                    'aria-label'=>'View target vs player card',
                    'data-pjax' => '0',
                  ]
              );?></b></p>
    <p><b><i class="far fa-calendar-alt text-warning"></i> <?=\Yii::$app->formatter->asDate($headshot->created_at,'long')?></b><br/>
    <?php if($headshot->writeup):?><b><i class="fas fa-book text-secondary"></i> Writeup submitted<?=$headshot->writeup->approved? '': ' ('.$headshot->writeup->status.')'?></b><br/><?php endif;?>
    <i class="fas fa-stopwatch text-danger"></i> <?=\Yii::$app->formatter->asDuration($headshot->timer)?>
    </p>
  </div>
</div>
<?php endforeach;?>
</div>
