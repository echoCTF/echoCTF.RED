<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use app\widgets\stream\StreamWidget as Stream;
$this->title=Yii::$app->sys->event_name.' Target: '.$target->name. ' / '.long2ip($target->ip). ' #'.$target->id;
$this->_description=$target->purpose;
$this->_image=\yii\helpers\Url::to($target->fullLogo, 'https');
$this->_url=\yii\helpers\Url::to(['view', 'id'=>$target->id], 'https');
$this->_fluid='-fluid';
?>

<div class="target-index">
  <div class="body-content">
<?php if($target->status !== 'online'):?>
    <div><p class="text-warning"><code class="text-warning">Target <?php if ($target->scheduled_at!==null):?>scheduled for<?php endif;?> <b><?=$target->status?></b> <?php if ($target->scheduled_at!==null):?>at <?=$target->scheduled_at?> UTC<?php endif;?></code></p></div>
<?php endif;?>
<?php if($target->network):?>
    <div><p class="text-info">Target from: <b><?=$target->network->name?></b></p></div>
<?php endif;?>

    <div class="watermarked img-fluid">
    <?=sprintf('<img src="%s" width="100px"/>', $target->logo)?>
    </div>
    <?php
    if(Yii::$app->user->isGuest)
      echo $this->render('_guest', ['target'=>$target, 'playerPoints'=>$playerPoints]);
    else
      echo $this->render('_versus', ['target'=>$target, 'playerPoints'=>$playerPoints, 'identity'=>Yii::$app->user->identity->profile]);
      ?>

        <?php \yii\widgets\Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);?>
        <?php echo Stream::widget(['divID'=>'target-activity', 'dataProvider' => $streamProvider, 'pagerID'=>'stream-pager', 'title'=>'Target activity', 'category'=>'Latest activity on the target']);?>
        <?php \yii\widgets\Pjax::end();?>
  </div>
</div>
