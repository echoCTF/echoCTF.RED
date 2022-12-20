<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */

$this->title=Yii::$app->sys->event_name.' Writeup for '.$model->target->name. ' #'.$model->target->id;
$this->_description=$model->target->purpose;
$this->_image=\yii\helpers\Url::to($model->target->fullLogo, 'https');
$this->_url=\yii\helpers\Url::to(['index', 'id'=>$model->target->id], 'https');
$this->_fluid='-fluid';
$this->registerJsFile('@web/js/showdown.min.js',[
  'depends' => [
    \yii\web\JqueryAsset::class
]
]);
?>
<div class="writeup-update">
  <div class="row">
    <div class="col-md-6 col-lg-7 body-content">
      <h2><?= Html::encode('Writeup for '.$model->target->name. ' #'.$model->target->id) ?></h2>
      <div class="col">
        <div class="card bg-dark">
          <div class="card-body">
            <h4 class="card-title <?=$model->status==='OK' ? 'text-primary' : 'text-warning'?>">Status: <?=$model->status?></h4>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-5">
      <h2>Preview</h2>
      <div id="markdown-preview" class="markdown" style="zoom: 80%;"></div>
    </div><!--/col-->
  </div>
</div>
