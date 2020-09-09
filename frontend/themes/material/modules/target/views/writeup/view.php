<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */
$this->title=Yii::$app->sys->event_name.' Writeup for '.$model->target->name. ' / '.long2ip($model->target->ip). ' #'.$model->target->id;
$this->_description=$model->target->purpose;
$this->_image=\yii\helpers\Url::to($model->target->fullLogo, 'https');
$this->_url=\yii\helpers\Url::to(['index', 'id'=>$model->target->id], 'https');
$this->_fluid='-fluid';
?>
<div class="writeup-view">
  <div class="body-content">
    <h2><?= Html::encode('Your writeup for '.$model->target->name. ' / '.long2ip($model->target->ip). ' #'.$model->target->id) ?></h2>
    <div class="col">
      <div class="card bg-dark">
        <div class="card-body">
          <h4 class="card-title <?=$model->status==='OK' ? 'text-primary' : 'text-warning'?>">Status: <?=$model->status?></h4>
          <pre style="color: lightgray;"><?=Html::encode($model->content);?></pre>
          <p>
          <?= Html::a('Update', ['update', 'id' => $model->target_id], ['class' => 'btn btn-primary']) ?>
          </p>
        </div>
      </div>
    </div><!--//col-->
    <?php if($model->comment!==NULL):?>
    <div class="col">
      <div class="card bg-dark">
        <div class="card-body">
          <h4 class="card-title">Comments from staff</h4>
          <pre style="color: lightgray;"><?=Html::encode($model->comment);?></pre>
        </div>
      </div>
    </div><!--//col-->
  <?php endif;?>

  </div>
</div>
