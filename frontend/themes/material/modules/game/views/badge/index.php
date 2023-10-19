<?php
use app\widgets\Twitter;
use yii\helpers\Html;

$this->registerMetaTag(['name'=>'og:type', 'content'=>'game.achievement']);
$this->registerMetaTag(['name'=>'game:points', 'content'=>'0']);
$this->registerMetaTag(['name'=>'article:published_time', 'content'=>$headshot->created_at]);
$this->_url=\yii\helpers\Url::to(['/game/badge/headshot', 'target_id'=>$headshot->target_id, 'profile_id'=>$headshot->player->profile->id], 'https');
?>
<center><img src="<?=$headshot->target->logo?>" width="128px"> <img class="img-fluid" style="max-width: 512px;" src="/images/logo.png" alt="echoCTF.RED">

<div class="card bg-dark" style="width: 60rem;">
  <div class="card-body">
    <h3 class="card-title"><?=\Yii::t('app','Target completion')?></h3>
    <p class="card-text lead"><code><?=$headshot->player->username?></code> <?=\Yii::t('app','has managed to complete the target')?> <code><?=$headshot->target->name?></code><?php if($headshot->target->timer && $headshot->timer>0):?> in <b><?=\Yii::$app->formatter->asDuration($headshot->timer)?></b>.<?php endif;?></p>
  </div>

  <?php echo $this->render('@app/modules/game/views/badge/_share',
      [
        'twMessage'=>sprintf(\Yii::t('app','Check this out, I just headshotted %s at %s'), $headshot->target->name, Html::encode(\Yii::$app->sys->{"event_name"})),
        'callbackURL'=>\yii\helpers\Url::to(['/game/badge/headshot', 'target_id'=>$headshot->target_id, 'profile_id'=>$headshot->player->profile->id], 'https'),
        'PRELINK'=>'<a class="btn btn-primary" href="#" onclick="history.back()"><i class="fas fa-reply"></i>&nbsp; Go back</a>'
      ]);?>

</div>
</center>
