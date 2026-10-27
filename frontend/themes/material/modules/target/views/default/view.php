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
<div id="target-ondemand-status-<?=$target->id?>"><?=$this->render('_ondemand_status',['target'=>$target])?></div>
<?php endif;?>
<?php if($target->status !== 'online'):?>
    <div><p class="text-warning"><code class="text-warning">Target <?php if ($target->scheduled_at!==null):?>scheduled for<?php endif;?> <b><?=$target->status?></b> <?php if ($target->scheduled_at!==null):?> <abbr title="<?=\Yii::$app->formatter->asDatetime($target->scheduled_at,'long')?>"><?=\Yii::$app->formatter->asRelativeTime($target->scheduled_at)?></abbr><?php endif;?></code></p></div>
<?php endif;?>
<?php if($target->network):?>
    <div><p class="text-info"><?=\Yii::t('app','Target from:')?> <b><?=Html::a($target->network->name,['/network/default/view','id'=>$target->network->id])?></b></p></div>
<?php endif;?>

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
