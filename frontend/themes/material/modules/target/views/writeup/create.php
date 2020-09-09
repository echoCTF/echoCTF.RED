<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */

$this->title=Yii::$app->sys->event_name.' Writeup for '.$headshot->target->name. ' / '.long2ip($headshot->target->ip). ' #'.$headshot->target->id;
$this->_description=$headshot->target->purpose;
$this->_image=\yii\helpers\Url::to($headshot->target->fullLogo, 'https');
$this->_url=\yii\helpers\Url::to(['index', 'id'=>$headshot->target->id], 'https');
$this->_fluid='-fluid';
?>
<div class="writeup-create">
  <div class="body-content">
    <h2><?= Html::encode('Writeup for '.$headshot->target->name. ' / '.long2ip($headshot->target->ip). ' #'.$headshot->target->id) ?></h2>
    <div class="col">
      <div class="card bg-dark">
        <div class="card-body">
          <h4 class="card-title <?=$model->status==='OK' ? 'text-primary' : 'text-danger'?>">Status: PENDING</h4>
          <?= $this->render('_form', [
              'model' => $model,
          ]) ?>
        </div>
      </div>
    </div>
  </div>
</div>
