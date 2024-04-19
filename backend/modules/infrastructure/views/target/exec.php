<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Target */

$this->title="Exec command on ".$model->name."/".$model->ipoctet." running on ".($model->server!="" ? $model->server : "localhost");
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Targets', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap5\Modal::begin([
    'header' => '<h2><i class="bi bi-info-circle-fill"></i>Target Exec Command Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
    'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/exec.md'), 'gfm');
yii\bootstrap5\Modal::end();

\yii\web\YiiAsset::register($this);
?>
<div class="target-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <p>
        <?= Html::a('View', ['view', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Logs', ['logs', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Generate', ['generate', 'id' => $model->id], ['class' => 'btn btn-info', 'style'=>'background-color: gray']) ?>
        <?= Html::a('Spin', ['spin', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Are you sure you want to restart the host?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Pull', ['pull', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'method' => 'post',
            ],
        ]) ?>

        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Destroy', ['destroy', 'id' => $model->id], [
            'class' => 'btn btn-info',
            'data' => [
                'confirm' => 'Are you sure you want to destroy the container for this item?',
                'method' => 'post',
            ],
        ]) ?>

    </p>
<div class="form">
<?php $form = ActiveForm::begin(); ?>
<div class="row">
  <div class="col-lg-8"><?= $form->field($formModel, 'command',[ 'inputOptions'=>['value'=>'','autocomplete'=>"off", 'class'=>'form-control','aria-required'=>"true"]])->label(false) ?></div>
  <div class="col"><?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?></div>
</div>
<div class="row">
  <div class="col-lg-1">
    <?=$form->field($formModel,'tty')->checkbox();?>
  </div>
  <div class="col-lg-1">
    <?=$form->field($formModel,'stdout')->checkbox();?>
  </div>
  <div class="col-lg-1">
    <?=$form->field($formModel,'stderr')->checkbox();?>
  </div>
</div>
<?php ActiveForm::end(); ?>
</div>
<pre class="stdout">
<b><?=Html::encode($formModel->command)."\n";?></b>
<?=Html::encode($stdout)?>
</pre>
<?php if($stderr):?>
<pre class="stderr">
<?=Html::encode($stderr)?>
</pre>
<?php endif;?>
</div>
