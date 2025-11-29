<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\modules\infrastructure\models\PrivateNetworkTarget $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="private-network-target-form">

  <?php $form = ActiveForm::begin(); ?>
  <?= $form->field($model, 'target_id')->dropDownList(
    ArrayHelper::map(app\modules\gameplay\models\Target::find()->orderBy('name')->all(), 'id', 'name'),
    ['prompt' => 'Select Target']
  )->Label('Target')->hint('The Target to assign to the network') ?>

  <?= $form->field($model, 'private_network_id')->dropDownList(
    ArrayHelper::map(app\modules\infrastructure\models\PrivateNetwork::find()->all(), 'id', 'name'),
    ['prompt' => 'Select a Private Network']
  )->Label('Private Network')->hint('The private network this target will belong') ?>

  <?= $form->field($model, 'server_id')->dropDownList(
    ArrayHelper::map(app\modules\infrastructure\models\Server::find()->all(), 'id', 'name'),
    ['prompt' => 'Select a Server']
  )->Label('Private Network')->hint('The server this machine is spawned') ?>

<?= $form->field($model, 'state')->textInput(['maxlength' => true])->hint('The target state (<code>default: 0</code>)') ?>
<?= $form->field($model, 'ipoctet')->textInput(['maxlength' => true])->hint('The target IP') ?>

  <div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>