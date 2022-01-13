<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\widgets\vote\VoteWidget;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */
$this->title=Yii::$app->sys->event_name.' '.'Writeup for '.$model->target->name. ' / '.long2ip($model->target->ip). ' #'.$model->target->id.' by '.$model->player->username;
$this->_description=$model->target->purpose;
$this->_image=\yii\helpers\Url::to($model->target->fullLogo, 'https');
$this->_url=\yii\helpers\Url::to(['view', 'id'=>$model->target->id], 'https');
$this->_fluid='-fluid';
$this->registerJsFile('@web/js/showdown.min.js',[
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$this->registerJsFile('@web/assets/hljs/highlight.min.js',[
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$this->registerCssFile('@web/assets/hljs/styles/a11y-dark.min.css',['depends' => ['app\assets\MaterialAsset']]);

$goback=Url::previous();
if($goback==='/')
  $goback=['/target/default/view','id'=>$model->target_id];

?>
<div class="writeup-view">
  <div class="body-content">
    <h2>Writeup for <?=Html::a($model->target->name. ' / '.long2ip($model->target->ip). ' #'.$model->target->id,$goback)?> by <?=$model->player->profile->link?></h2>
    <div class="row">
      <div class="col-md-8">
        <div class="card bg-dark">
          <div class="card-header">
            <div class="row"><h4 class="align-self-center">Your rating:</h4> <div class="col-sm-5"><?=VoteWidget::widget(['model'=>$rating,'id'=>$model->id,'action'=>['/game/default/rate-writeup','target_id'=>$model->target_id,'id'=>$model->id],'ratings'=>$model->_ratings]);?></div></div>
          </div>
          <div class="card-body">
            <div id="markdown-content" class="markdown">
              <?=$model->formatted?>
            </div>
          </div>
        </div>
      </div><!--//col-->
      <div class="col-md-4">
        <?=$this->render('../default/_target_card',['target'=>$model->target,'spinlink'=>null]);?>
        <?=$this->render('../default/_target_writeups',['writeups'=>$model->target->writeups,'active'=>$model->id]);?>
      </div>
    </div>
  </div>
</div>
<?php
$this->registerJs(
  'hljs.highlightAll();',
  $this::POS_READY,
  'markdown-highlighter'
);
?>
