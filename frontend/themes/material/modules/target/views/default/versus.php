<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use app\widgets\stream\StreamWidget as Stream;
$this->title=\Yii::t('app','{event_name} Target: {target_name} vs Player: {username}',['event_name'=>Yii::$app->sys->event_name,'target_name'=>$target->name,'username'=>$profile->owner->username]);
$this->_description=$target->purpose;
$this->_image=\yii\helpers\Url::to($target->fullLogo, 'https');
$this->_url=\yii\helpers\Url::to(['versus', 'id'=>$target->id, 'profile_id'=>$profile->id], 'https');
$this->loadLayoutOverrides=true;
$this->_fluid='-fluid';
Url::remember();
?>

<div class="target-index">
  <div class="body-content">
<?php if($target->status !== 'online'):?>
    <div><p class="text-warning"><code class="text-warning">Target <?php if ($target->scheduled_at!==null):?>scheduled for<?php endif;?> <b><?=$target->status?></b> <?php if ($target->scheduled_at!==null):?>at <?=$target->scheduled_at?> UTC<?php endif;?></code></p></div>
<?php endif;?>
<?php if($target->network):?>
    <div><p class="text-info"><?=\Yii::t('app','Target from:')?> <b><?=$target->network->name?></b></p></div>
<?php endif;?>
    <div class="watermarked img-fluid">
    <?=sprintf('<img src="%s" width="100px"/>', $target->logo)?>
    </div>
    <?php echo $this->render('_versus', ['target'=>$target, 'playerPoints'=>$playerPoints, 'identity'=>$profile]);?>

        <?php \yii\widgets\Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);?>
        <?php echo Stream::widget(['divID'=>'target-activity', 'dataProvider' => $streamProvider, 'pagerID'=>'stream-pager', 'title'=>\Yii::t('app','Target activity'), 'category'=>\Yii::t('app','Latest activity on the target')]);?>
        <?php \yii\widgets\Pjax::end();?>
  </div>
</div>
