<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use app\widgets\stream\StreamWidget as Stream;
$this->title = Yii::$app->sys->event_name .' Target: '.$target->name.' #'.$target->id;
$this->_description =$target->purpose;
$this->_image=\yii\helpers\Url::to($target->fullLogo,'https');
$this->_url=\yii\helpers\Url::to(['index','id'=>$target->id],'https');
$this->_fluid='-fluid';
?>

<div class="target-index">
  <div class="body-content">
    <?php if ($target->status!=='online'):?>
    <div><p class="text-warning">Target scheduled for <b><?=$target->status?></b> at <code class="text-warning"><?=$target->scheduled_at?> UTC</code></p></div>
    <?php endif;?>
    <div class="watermarked img-fluid">
    <?=sprintf('<img src="%s" width="100px"/>',$target->logo)?>
    </div>
    <?php
    if(Yii::$app->user->isGuest)
      echo $this->render('_guest',['target'=>$target,'playerPoints'=>$playerPoints]);
    else
      echo $this->render('_versus',['target'=>$target,'playerPoints'=>$playerPoints,'identity'=>Yii::$app->user->identity->profile]);
     ?>

        <?php \yii\widgets\Pjax::begin(['id'=>'stream-listing','enablePushState'=>false,'linkSelector'=>'#stream-pager a', 'formSelector'=>false]); ?>
        <?php echo Stream::widget(['divID'=>'target-activity','dataProvider' => $streamProvider,'pagerID'=>'stream-pager','title'=>'Target activity','category'=>'Latest activity on the target']);?>
        <?php \yii\widgets\Pjax::end(); ?>
  </div>
</div>
