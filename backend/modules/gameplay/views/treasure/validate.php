<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Treasure */

$this->title = 'Validate Treasure';
$this->params['breadcrumbs'][] = ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => 'Treasures', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Validate', 'url' => ['validate']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options' => ['class' => 'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="treasure-update">

  <h1><?= Html::encode($this->title) ?></h1>
  <?= Html::beginForm(['/gameplay/treasure/validate'], 'post') ?>
  <div class="form-group">
    <?= Html::label('Code', 'code') ?>
    <?= Html::textInput('code', $code, ['class' => 'form-control', 'id' => 'code', 'required' => true, 'autocomplete' => 'off']) ?>
  </div>

  <div class="form-group">
    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
  </div>

  <?= Html::csrfMetaTags() ?>

  <?= Html::endForm() ?>

</div>