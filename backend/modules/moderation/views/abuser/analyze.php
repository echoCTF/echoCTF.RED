<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\moderation\models\Abuser $model */

$this->title = Yii::t('app', 'Abuser: {player} / {reason} / {name}', [
  'name' => $model->title,
  'reason' => $model->reason,
  'player' => $model->player->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Abusers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="abuser-update">

  <h1><?= Html::encode($this->title) ?></h1>
  <div class="abuser-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'reason')->textInput()->hint('Penalty reason') ?>
    <?= $form->field($model, 'points')->textInput()->hint('Penalty points') ?>
    <div class="form-group">
      <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'giver']) ?>
      <?= Html::submitButton(Yii::t('app', 'Save with Giver'), ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'giver']) ?>
      <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
          'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
          'method' => 'post',
        ],
      ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

  </div>


</div>