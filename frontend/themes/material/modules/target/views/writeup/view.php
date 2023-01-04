<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */
$this->title=\Yii::t('app','{event_name} Writeup for {target_name} #{target_id}',['event_name'=>Yii::$app->sys->event_name,'target_name'=>$model->target->name,'target_id'=>$model->target->id]);
$this->_description=$model->target->purpose;
$this->_image=\yii\helpers\Url::to($model->target->fullLogo, 'https');
$this->_url=\yii\helpers\Url::to(['view', 'id'=>$model->target->id], 'https');
$this->_fluid='-fluid';
$this->loadLayoutOverrides=true;
?>
<div class="writeup-view">
  <div class="body-content">
    <h2><?= Html::encode('Your writeup for '.$model->target->name. ' #'.$model->target->id) ?></h2>
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
    <?php if($model->comment!==null):?>
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
