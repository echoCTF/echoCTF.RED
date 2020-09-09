<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */

$this->title=Yii::$app->sys->event_name.' Writeup for '.$model->target->name. ' / '.long2ip($model->target->ip). ' #'.$model->target->id;
$this->_description=$model->target->purpose;
$this->_image=\yii\helpers\Url::to($model->target->fullLogo, 'https');
$this->_url=\yii\helpers\Url::to(['index', 'id'=>$model->target->id], 'https');
$this->_fluid='-fluid';
?>
<div class="writeup-update">
  <div class="body-content">
    <h2><?= Html::encode('Writeup for '.$model->target->name. ' / '.long2ip($model->target->ip). ' #'.$model->target->id) ?></h2>
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
</div>
