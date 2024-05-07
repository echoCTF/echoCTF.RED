<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\widgets\vote\VoteWidget;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */
$this->title=\Yii::t('app','{event_name} Writeup for {target_name} by {username}',['event_name'=>Yii::$app->sys->event_name,'target_name'=>$model->target->name,'target_id'=>$model->target->id,'username'=>$model->player->username]);
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
    <h2><?=\Yii::t('app','({language}) Writeup for {target_name} by {username}',['target_name'=>Html::a($model->target->name,$goback),'username'=>$model->player->profile->link,'language'=>$model->language->l]);?></h2>
    <div class="row">
      <div class="col-md-8">
        <div class="card bg-dark">
          <div class="card-header">
            <div class="row"><h4 class="align-self-center"><?=\Yii::t('app','Your rating:')?></h4> <div class="col-sm-5"><?=VoteWidget::widget(['model'=>$rating,'id'=>$model->id,'action'=>['/game/default/rate-writeup','target_id'=>$model->target_id,'id'=>$model->id],'ratings'=>$model->_ratings]);?></div></div>
          </div>
          <div class="card-body">
            <div id="markdown-content" class="markdown">
              <?=$model->formatted?>
            </div>
          </div>
        </div>
      </div><!--//col-->
      <div class="col-md-4">
        <?=$this->render('../default/_target_card',['target'=>$model->target,'identity'=>Yii::$app->user->identity->profile]);?>
        <?=$this->render('../default/_target_writeups',['writeups'=>$model->target->writeups,'active'=>$model->id, 'writeups_activated'=>true]);?>
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
