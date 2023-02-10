<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use app\widgets\stream\StreamWidget as Stream;
$this->title=\Yii::t('app','{event_name} Target: {target_name}',['event_name'=>Yii::$app->sys->event_name,'target_name'=>$target->name]);
$this->_description=$target->purpose;
$this->_image=\yii\helpers\Url::to($target->fullLogo, 'https');
$this->_url=\yii\helpers\Url::to(['view', 'id'=>$target->id], 'https');
$this->_fluid='-fluid';
Url::remember();
?>

<div class="target-index">
  <div class="body-content">
<?php if(!Yii::$app->user->isGuest):?>
  <?php if($target->ondemand && $target->ondemand->state<0):?>
    <div><p class="text-info"><?=\Yii::t('app','This target is currently powered off.')?> <?php if(Yii::$app->user->identity->profile->last->vpn_local_address===null):?><em><?=\Yii::t('app','Connect to the VPN to be allowed to power the system up.')?></em><?php endif;?></p></div>
  <?php elseif($target->ondemand && $target->ondemand->state>0):?>
    <div><p class="text-danger"><?=\Yii::t('app','The target will shutdown in')?> <code id="tcountdown" data="<?=$target->ondemand->expired?>"></code></p></div>
    <?php $this->registerJs(
    'var distance = $("#tcountdown").attr("data");
    var tcountdown = setInterval(function() {
      var minutes = Math.floor((distance % (60 * 60)) / ( 60));
      var seconds = Math.floor((distance % (60)));
      if (distance < 0) {
        clearInterval(tcountdown);
        document.getElementById("tcountdown").innerHTML = "'.\Yii::t('app','system will shutdown soon!').'";
      }
      else {
        document.getElementById("tcountdown").innerHTML = '.\Yii::t('app','minutes + "m " + seconds + "s "').';
        $("#tcountdown").attr("data",distance--);
      }
      }, 1000);',
    4
    );?>
  <?php endif;?>
<?php endif;?>
<?php if($target->status !== 'online'):?>
    <div><p class="text-warning"><code class="text-warning">Target <?php if ($target->scheduled_at!==null):?>scheduled for<?php endif;?> <b><?=$target->status?></b> <?php if ($target->scheduled_at!==null):?> <abbr title="<?=\Yii::$app->formatter->asDatetime($target->scheduled_at,'long')?>"><?=\Yii::$app->formatter->asRelativeTime($target->scheduled_at)?></abbr><?php endif;?></code></p></div>
<?php endif;?>
<?php if($target->network):?>
    <div><p class="text-info"><?=\Yii::t('app','Target from:')?> <b><?=Html::a($target->network->name,['/network/default/view','id'=>$target->network->id])?></b></p></div>
<?php endif;?>

<div class="watermarked img-fluid">
<img src="<?=$target->logo?>" width="100px"/>
</div>

<?php
if(Yii::$app->user->isGuest)
{
  echo $this->render('_guest', ['target'=>$target, 'playerPoints'=>$playerPoints,'streamProvider'=>$streamProvider->getTotalCount()]);
}
else
{
  echo $this->render('_versus', ['target'=>$target, 'playerPoints'=>$playerPoints, 'identity'=>Yii::$app->user->identity->profile]);

  \yii\widgets\Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
  echo Stream::widget(['divID'=>'target-activity', 'dataProvider' => $streamProvider, 'pagerID'=>'stream-pager', 'title'=>\Yii::t('app','Target activity'), 'category'=>\Yii::t('app','Latest activity on the target')]);
  \yii\widgets\Pjax::end();
}
?>

  </div>
</div>
