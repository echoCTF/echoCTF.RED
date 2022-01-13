<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\widgets\vote\VoteWidget;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */
$_title='Writeup for '.$model->target->name. ' / '.long2ip($model->target->ip). ' #'.$model->target->id.' by '.$model->player->username;
$this->title=Yii::$app->sys->event_name.' '.$_title;
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
$ratings=[
  [ 'id'=>0, 'name' => "Not rated!", 'icon'=>null],
  [ 'id'=>1,  'name' => "1 - Ok", 'icon'=>'fa-battery-quarter red-success',],
  [ 'id'=>2,  'name' => "2 - Nice", 'icon'=>'fa-battery-half text-secondary',],
  [ 'id'=>3,  'name' => "3 - Good", 'icon'=>'fa-battery-three-quarters text-warning',],
  [ 'id'=>4,  'name' => "4 - Well written", 'icon'=>'fa-battery-full',],
  [ 'id'=>5,  'name' => "5 - Excellent", 'icon'=>'fa-battery-full',],
];
?>
<div class="writeup-view">
  <div class="body-content">
    <h2><?= Html::encode($_title)?></h2>
    <div class="row">
      <div class="col-md-8">
        <div class="card bg-dark">
          <div class="card-header">
            <div class="row"><h4 class="align-self-center">Your rating:</h4> <div class="col-sm-5"><?=VoteWidget::widget(['model'=>$rating,'id'=>$model->id,'action'=>['/game/default/rate-writeup','target_id'=>$model->target_id,'id'=>$model->id],'ratings'=>$ratings]);?></div></div>
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
/*


$this->registerJs(
    'const textarea=document.getElementById("writeup-content");
     var converter = new showdown.Converter({
         omitExtraWLInCodeBlocks: true,
         headerLevelStart: 2,
         parseImgDimensions: true,
         ghCodeBlocks: true,
         simplifiedAutoLink: true,
         tables: true,
         tasklists: true,
         simpleLineBreaks: true,
         openLinksInNewWindow: true,
         emoji: true,
         splitAdjacentBlockquotes: true,
       });
     converter.setFlavor(\'github\');
     var text      = textarea.innerHTML,
         html      = converter.makeHtml(text);
     document.getElementById("markdown-preview").innerHTML=html;
     ',
    $this::POS_READY,
    'render-markdown'
);
*/
//$this->registerJs(
//    "$('#toggle-button').on('click', function() {
//      document.getElementById('markdown-preview').style.display=(document.getElementById('markdown-preview').style.display=='none' ? '' : 'none');
//      textarea.style.display=(textarea.style.display=='none' ? '' : 'none');
//    });",
//    $this::POS_READY,
//    'toggle-button-handler'
//);
?>
