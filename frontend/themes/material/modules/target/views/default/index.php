<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use app\widgets\stream\StreamWidget as Stream;
$this->title = Yii::$app->sys->event_name .' Target: '.$target->name.' #'.$target->id;
$this->_description =$target->purpose;
$this->_fluid='-fluid';
?>

<div class="target-index">
  <div class="body-content">
    <div class="watermarked img-fluid">
    <?=sprintf('<img src="/images/targets/_%s.png" width="100px"/>',$target->name)?>
    </div>

    <?php
    if(Yii::$app->user->isGuest)
      echo $this->render('_guest',['target'=>$target,'playerPoints'=>$playerPoints]);
    else
      echo $this->render('_versus',['target'=>$target,'playerPoints'=>$playerPoints]);
     ?>

        <?php \yii\widgets\Pjax::begin(['id'=>'stream-listing','enablePushState'=>false,'linkSelector'=>'#stream-pager a', 'formSelector'=>false]); ?>
        <?php echo Stream::widget(['divID'=>'target-activity','dataProvider' => $streamProvider,'pagerID'=>'stream-pager','title'=>'Target activity','category'=>'Latest activity on the target']);?>
        <?php \yii\widgets\Pjax::end(); ?>
  </div>
</div>
